<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Subscription;
use App\Models\User;

use App\Services\PriceFetcherService;

class SubscriptionController extends Controller
{
    private $priceFetcher;

    public function __construct(PriceFetcherService $priceFetcher)
    {
        $this->priceFetcher = $priceFetcher;
    }

    /**
     * @OA\Get(
     *     path="/subscriptions",
     *     summary="Get current user`s list of subscriptions",
     *     operationId="indexSubscription",
     *     tags={"Subscription"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
                name="user_id",
     *          in="query",
     *          description="ID користувача",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Subscription"))
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to get subscriptions",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", example="Failed to get subscriptions")
     *          )
     *     )
     *  )
     */
    // Display a list of subscriptions for the authenticated user
    public function index(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            if ($userId) {
                $user = User::findOrFail($userId);
            } else {
                $user = Auth::user();
            }

            $subscriptions = $user->subscriptions;

            return response()->json($subscriptions, 200);
        } catch (\Exception $e) {
            Log::error('Failed to get subscriptions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get subscriptions'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/subscriptions",
     *     summary="Create a new subscription",
     *     description="Validates the provided URL and adds a new subscription for the authenticated user.",
     *     operationId="storeSubscription",
     *     tags={"Subscription"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"url"},
     *              @OA\Property(
     *                  property="url",
     *                  type="string",
     *                  format="url",
     *                  example="https://example.com"
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=201,
     *          description="Subscription added successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Subscription added successfully."),
     *              @OA\Property(property="subscription", ref="#/components/schemas/Subscription")
     *          )
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create subscription",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Failed to create subscription"
     *              )
     *          )
     *     )
     *  )
     */
    // Add a new subscription for the authenticated user
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'url' => 'required|url|unique:subscriptions,url',
            ]);

            $subscription = Subscription::firstOrCreate(['url' => $validated['url']]);
            $user = Auth::user();

            // Attach subscription to user
            if (!$user->subscriptions->contains($subscription->id)) {
                $user->subscriptions()->attach($subscription->id);
            }

            return response()->json(['message' => 'Subscription added successfully.', 'subscription' => $subscription], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create subscription'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/subscriptions/{id}",
     *     summary="Remove a subscription",
     *     description="Removes a subscription for the authenticated user by subscription ID.",
     *     operationId="destroySubscription",
     *     tags={"Subscription"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the subscription to remove",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subscription removed successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subscription not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subscription not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete subscription",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="error", type="string", example="Failed to delete subscription")
     *          )
     *     ),
     *
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */

    // Remove a subscription for the authenticated user
    public function destroy($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $user = Auth::user();

            // Detach the subscription from the user
            $user->subscriptions()->detach($subscription->id);

            return response()->json(['message' => 'Subscription removed successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete subscription: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete subscription'], 500);
        }

    }

    /**
     * @OA\Post(
     *     path="/testurl",
     *     summary="Test URL for price",
     *     description="Fetches the price from the given URL and returns it.",
     *     operationId="testUrlPrice",
     *     tags={"Test"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 format="url",
     *                 example="https://example.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Price got successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Price got successfully."),
     *             @OA\Property(property="price", type="number", format="float", example=99.99),
     *             @OA\Property(property="url", type="string", format="url", example="https://example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to test get price from url",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Failed to test get price from url"
     *              )
     *          )
     *     )
     * )
     */

    //test get price by url
    public function test(Request $request)
    {
        try {
            $validated = $request->validate([
                'url' => 'required|url|unique:subscriptions,url',
            ]);
            $url = $validated['url'];
            $price = $this->priceFetcher->fetchPrice($url);

            return response()->json(['message' => 'Price got successfully.', 'price' => $price, 'url' => $url], 200);
        } catch (\Exception $e) {
            Log::error('Failed to test get price from url: ' . $url . $e->getMessage());
            return response()->json(['error' => 'Failed to test get price from url'], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/test2",
     *     summary="Test URL for price",
     *     description="Fetches the price from the given URL and returns it.",
     *     operationId="test2",
     *     tags={"Test"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 format="url",
     *                 example="https://example.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Price got successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Price got successfully."),
     *             @OA\Property(property="url", type="string", format="url", example="https://example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function test2(Request $request)
    {

        $validated = $request->validate([
            'url' => 'required|url',
        ]);
        $url = $validated['url'];
        //$price = $this->priceFetcher->fetchPrice($url);

        return response()->json(['message' => 'Price got successfully.', 'url' => $url], 200);

    }
}
