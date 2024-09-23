<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Utility extends Model
{
    private static $getsettings = null;
    private static $languages = null;
    private static $settings = null;
    private static $payments = null;
    private static $cookies = null;

    public static function settings()
    {
        if (!self::$settings) {
            self::$settings = self::fetchSettings();
        }

        return self::$settings;
    }

    public static function fetchSettings($user_id = null)
    {
        $data = DB::table('settings');
        if ($user_id != null) {
            $user = User::where('id', $user_id)->first();
            if ($user != null) {
                $data = $data->where('created_by', '=', $user_id)->get();
            } else {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
        }

        if (Auth::check()) {
            $data = $data->where('created_by', '=', Auth::id())->get();
            if (count($data) == 0) {
                $data = DB::table('settings')->where('created_by', '=', 1)->get();
            }
        } else {
            $data = DB::table('settings')->where('created_by', '=', 1)->get();
        }

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "employee_prefix" => "#EMP00",
            "footer_title" => "",
            "footer_notes" => "",
            "company_start_time" => "09:00",
            "company_end_time" => "18:00",
            'new_user' => '1',
            'new_employee' => '1',
            'new_payroll' => '1',
            'new_ticket' => '1',
            'new_award' => '1',
            'employee_transfer' => '1',
            'employee_resignation' => '1',
            'employee_trip' => '1',
            'employee_promotion' => '1',
            'employee_complaints' => '1',
            'employee_warning' => '1',
            'employee_termination' => '1',
            'leave_status' => '1',
            'contract' => '1',
            "default_language" => "en",
            "display_landing_page" => "on",
            "ip_restrict" => "on",
            "title_text" => "",
            "footer_text" => "",
            "gdpr_cookie" => "",
            "cookie_text" => "",
            "metakeyword" => "",
            "metadesc" => "",
            "zoom_account_id" => "",
            "zoom_client_id" => "",
            "zoom_client_secret" => "",
            'disable_signup_button' => "on",
            "theme_color" => "theme-3",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "SITE_RTL" => "off",
            "company_logo" => 'logo-dark.png',
            "company_logo_light" => 'logo-light.png',
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "contract_prefix" => "#CON",
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "google_clender_id" => "",
            "google_calender_json_file" => "",
            "is_enabled" => "",
            "email_verification" => "",
            // "seo_is_enabled" => "",
            "meta_title" => "",
            "meta_image" => "",
            "meta_description" => "",
            'enable_cookie' => 'on',
            'necessary_cookies' => 'on',
            'cookie_logging' => 'on',
            'cookie_title' => 'We use cookies!',
            'cookie_description' => 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it',
            'strictly_cookie_title' => 'Strictly necessary cookies',
            'strictly_cookie_description' => 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
            'more_information_description' => 'For any queries in relation to our policy on cookies and your choices, please contact us',
            'contactus_url' => '#',
            'chatgpt_key' => '',
            'chatgpt_model' => '',
            'enable_chatgpt' => '',
            'mail_driver' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',
            'timezone' => '',
            'pusher_app_id' => '',
            'pusher_app_key' => '',
            'pusher_app_secret' => '',
            'pusher_app_cluster' => '',
            'recaptcha_module' => '',
            'google_recaptcha_key' => '',
            'google_recaptcha_secret' => '',
            'color_flag' => 'false',
            'zkteco_api_url' => '',
            'username' => '',
            'user_password' => '',
            'auth_token' => '',
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function languages()
    {
        if (self::$languages === null) {
            self::$languages = self::fetchlanguages();
        }

        return self::$languages;
    }

    public static function fetchlanguages()
    {
        $languages = Utility::langList();

        if (Schema::hasTable('languages')) {
            $settings = self::settings();
            if (!empty($settings['disable_lang'])) {
                $disablelang = explode(',', $settings['disable_lang']);
                $languages = Languages::whereNotIn('code', $disablelang)->pluck('fullname', 'code');
            } else {
                $languages = Languages::pluck('fullname', 'code');
            }
        }
        return $languages;
    }

    public static function langList()
    {
        $languages = [
            "ar" => "Arabic",
            "zh" => "Chinese",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "he" => "Hebrew",
            "it" => "Italian",
            "ja" => "Japanese",
            "nl" => "Dutch",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ru" => "Russian",
            "tr" => "Turkish",
            "pt-br" => "Portuguese(Brazil)"
        ];

        return $languages;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();
        if (!isset($setting[$key]) || empty($setting[$key])) {
            $setting[$key] = '';
        }
        return $setting[$key];
    }

    public static function get_file($path)
    {
        $settings = self::settings();

        try {
            if ($settings['storage_setting'] == 'wasabi') {
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com'
                    ]
                );
            } elseif ($settings['storage_setting'] == 's3') {

                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }

            return Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }

    public static function GetLogo()
    {
        $setting = Utility::colorset();
        if (\Auth::user() && \Auth::user()->type != 'super admin') {

            if (Utility::getValByName('cust_darklayout') == 'on') {

                return Utility::getValByName('company_logo_light');
            } else {
                return Utility::getValByName('company_logo');
            }
        } else {
            if (Utility::getValByName('cust_darklayout') == 'on') {
                return Utility::getValByName('light_logo');
            } else {
                return Utility::getValByName('dark_logo');
            }
        }
    }

    public static function colorset()
    {
        if (self::$settings === null) {
            self::$settings = self::fetchcolorset();
        }
        return self::$settings;
    }

    public static function fetchcolorset()
    {
        if (\Auth::user()) {
            if (\Auth::user()->type == 'super admin') {
                $user = \Auth::user();

                $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
            } else {
                $setting = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->pluck('value', 'name')->toArray();
            }
        } else {
            $user = User::where('type', 'super admin')->first();
            $setting = DB::table('settings')->where('created_by', $user->id)->pluck('value', 'name')->toArray();
        }
        if (!isset($setting['color'])) {
            $setting = Utility::settings();
        }
        return $setting;
    }

    public static function getSeoSetting()
    {
        $data = \DB::table('settings')->whereIn('name', ['meta_title', 'meta_description', 'meta_image'])->get();
        $settings = [];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function getCookieSetting()
    {
        if (self::$cookies === null) {
            self::$cookies = self::fetchCookieSetting();
        }
        return self::$cookies;
    }

    public static function fetchCookieSetting()
    {
        $data = \DB::table('settings')->whereIn('name', [
            'enable_cookie', 'cookie_logging', 'cookie_title',
            'cookie_description', 'necessary_cookies', 'strictly_cookie_title',
            'strictly_cookie_description', 'more_information_description', 'contactus_url'
        ])->get();
        $settings = [
            'enable_cookie' => 'off',
            'necessary_cookies' => '',
            'cookie_logging' => '',
            'cookie_title' => '',
            'cookie_description' => '',
            'strictly_cookie_title' => '',
            'strictly_cookie_description' => '',
            'more_information_description' => '',
            'contactus_url' => '',
        ];
        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }
}
