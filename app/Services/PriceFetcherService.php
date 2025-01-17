<?php
namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class PriceFetcherService
{

    public function fetchPrice(string $url): ?float
    {
        $client = new Client();
        $response = $client->get($url);
        $html = (string)$response->getBody();

        $crawler = new Crawler($html);

        //<div data-testid="ad-price-container" class="css-e2ir3r"><h3 class="css-90xrc0">2 840 365 грн.</h3></div>
        try {
            //$priceNode = $crawler->filter('[data-testid="ad-price-container"] h3.css-90xrc0')->first();
            //$priceText = $priceNode->text();
            $priceText = $crawler->filter('div[data-testid="ad-price-container"] h3')->text();

            // Delete all chars, except digits & point
            $price = preg_replace('/[^\d.]/', '', str_replace(',', '.', $priceText));

            return (float)$price;
        } catch (\Exception $e) {
            Log::error("Price not found on URL: $url", ['exception' => $e]);
            return null;
        }
    }

    public function updatePrices(): void
    {
        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {
            $currentPrice = $this->fetchPrice($subscription->url);

            if ($currentPrice !== null && $subscription->last_checked_price !== $currentPrice) {
                $subscription->last_checked_price = $currentPrice;
                $subscription->save();

                foreach ($subscription->users as $user) {
                    // Notify the user (placeholder logic)
                    Log::info("Notifying user", [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'new_price' => $currentPrice
                    ]);
                }
            }
        }
    }
}
