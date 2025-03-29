<?php

namespace App\Http\Controllers;


use App\Models\Package;
use App\Models\Module;
use App\Enums\PackageType;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Support\Facades\File;
use App\Models\Restaurant;

class HomeController extends Controller
{

    use AppBoot;

    public function landing()
    {

        $this->showInstall();

        if (global_setting()->landing_site_type == 'custom') {
            return response(file_get_contents(global_setting()->landing_site_url));
        }

        $this->modules = Module::pluck('name')->toArray();
        $this->PackageFeatures = Package::ADDITIONAL_FEATURES;

        $AllModulesWithFeature = array_merge(
            $this->modules,
            $this->PackageFeatures
        );

        $packages = Package::with('modules')
            ->where('package_type', '!=', PackageType::DEFAULT)
            ->where('package_type', '!=', PackageType::TRIAL)
            ->where('is_private', false)
            ->get();

        $trialPackage = Package::where('package_type', PackageType::TRIAL)->first();


        return view('landing.index', compact('packages', 'AllModulesWithFeature', 'trialPackage'));
    }

    public function signup()
    {
        if (global_setting()->disable_landing_site) {
            return view('auth.restaurant_register');
        }

        return view('auth.restaurant_signup');
    }

    public function customerLogout()
    {
        session()->flush();
        return redirect('/');
    }

    public function manifest()
    {
        $hash = request()->query('hash', '');

        if($hash == 'landing'){
            $slug = 'super-admin/';
        }else{
            $slug = 'restaurant/' . $hash . '/';
        }

        $basePath = '';
        if (!empty($hash)) {
            $parsedUrl = parse_url($hash);
            if (isset($parsedUrl['path'])) {
                $basePath = url($parsedUrl['path']);
            }
            if (isset($parsedUrl['query'])) {
                $queryParams = '?' . $parsedUrl['query'];
            }
        }else {
            $basePath = url('/');
        }

        $firstimagePath = public_path('user-uploads/favicons/' . $slug . 'android-chrome-192x192.png');
        $secondimagePath = public_path('user-uploads/favicons/' . $slug . 'android-chrome-512x512.png');
        $firsticonUrl = File::exists($firstimagePath) ? asset('user-uploads/favicons/' . $slug . 'android-chrome-192x192.png') : asset('img/192x192.png');
        $secondiconUrl = File::exists($secondimagePath) ? asset('user-uploads/favicons/' . $slug . 'android-chrome-512x512.png') : asset('img/512x512.png');
        $globalSetting = global_setting();
        $restaurant = Restaurant::where('hash', $hash)->first();

        $queryParams = $queryParams ?? '';

        return response()->json([

            "name" => $restaurant ? $restaurant->name : $globalSetting->name,
            "short_name" => $restaurant ? $restaurant->name : $globalSetting->name,
            "description" =>  $restaurant ? $restaurant->name : $globalSetting->name,
            "start_url_base" => $basePath,
            "query_params" => $queryParams,
            "display" => "standalone",
            "background_color" => "#ffffff",
            "theme_color" => "#000000",
            "icons" => [
                [
                    "src" => $firsticonUrl,
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => $secondiconUrl,
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ]
        ]);
    }
}
