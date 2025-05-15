<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenseTier;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // General settings
    public function index()
    {
        return view('admin.settings.index', [
            'settings' => Setting::firstOrNew(),
            'title' => 'General Settings',
        ]);
    }

    public function update(Request $request)
    {
        $settings = Setting::firstOrNew();
        $input = $request->except(['logo', 'favicon']);

        $input['registration_active'] = $request->boolean('registration_active');
        if ($request->hasFile('favicon')) {
            $image = $request->file('favicon');
            $imageName = \Str::random(5).'-favicon.png';
            $image->move(public_path('uploads'), $imageName);
            $input['favicon'] = $imageName;
        }

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = \Str::random(5).'-logo.png';
            $image->move(public_path('uploads'), $imageName);
            $input['logo'] = $imageName;
        }
        $settings->fill($input)->save();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => __('Settings Updated Successfully.')]);
        }

        return redirect()->back()->with('success', __('Settings Updated Successfully.'));
    }

    // License tiers management
    public function tiers()
    {
        return view('admin.settings.index', [
            'tiers' => LicenseTier::orderBy('order')->get(),
            'title' => 'License Tiers',
        ]);
    }

    public function updateTiers(Request $request)
    {
        $request->validate([
            'tiers' => 'required|array',
            'tiers.*.name' => 'required|string|max:255',
            'tiers.*.price' => 'required|numeric|min:0',
            'tiers.*.duration' => 'required|integer|min:1',
            'tiers.*.status' => 'integer',
            'tiers.*.order' => 'integer',
        ]);

        foreach ($request->tiers as $tierData) {
            LicenseTier::updateOrCreate(
                ['id' => $tierData['id'] ?? null],
                [
                    'name' => $tierData['name'],
                    'price' => $tierData['price'],
                    'duration' => $tierData['duration'],
                    'status' => $tierData['status'] ?? false,
                    'order' => $tierData['order'] ?? 1,
                ]
            );
        }

        // Remove deleted tiers
        if ($request->has('removed_tiers')) {
            LicenseTier::destroy($request->removed_tiers);
        }

        return back()->with('success', 'License tiers updated successfully');
    }
}
