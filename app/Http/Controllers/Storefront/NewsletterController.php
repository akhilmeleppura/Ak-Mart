<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower(trim($request->email))],
            [
                'status'        => 'subscribed',
                'source'        => 'storefront_footer',
                'subscribed_at' => now(),
            ]
        );

        return back()->with('success', 'Thank you for subscribing to AK-Mart weekly deals & flyers!');
    }
}
