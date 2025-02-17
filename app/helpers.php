<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Google\Client as Google_Client;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

function randomString($length)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $token = substr(str_shuffle($chars), 0, $length);

    return $token;
}

function mail_footer($type, $notify_message)
{
    return [
        'notification_type' => $type,
        'notification_message' => $notify_message,
        'logged_in_user_fullname' => auth()->user() ? auth()->user()->full_name ?? default_user_name() : '',
        'logged_in_user_role' => auth()->user() ? auth()->user()->getRoleNames()->first()->name ?? '-' : '',
        'company_name' => setting('app_name'),
        'company_contact_info' => implode('', [
            setting('helpline_number') . PHP_EOL,
            setting('inquriy_email'),
        ]),
    ];
}

function sendNotification($data)
{
    $mailable = \Modules\NotificationTemplate\Models\NotificationTemplate::where('type', $data['notification_type'])->with('defaultNotificationTemplateMap')->first();
    if ($mailable != null && $mailable->to != null) {
        $mails = json_decode($mailable->to);

        foreach ($mails as $key => $mailTo) {
            $data['type'] = $data['notification_type'];
            $booking = isset($data['booking']) ? $data['booking'] : null;
            $order = isset($data['order']) ? $data['order'] : null;
            $package = isset($data['package']) ? $data['package'] : null;
            if (isset($booking) && $booking != null) {
                $data['id'] = $booking['id'];
                // $data['logo'] = $booking['logo'];
                $data['description'] = $booking['description'];
                $data['user_id'] = $booking['user_id'];
                $data['user_name'] = $booking['user_name'];
                $data['employee_id'] = $booking['employee_id'];
                $data['employee_name'] = $booking['employee_name'];
                $data['booking_date'] = $booking['booking_date'];
                $data['booking_time'] = $booking['booking_time'];
                $data['booking_duration'] = $booking['booking_duration'];
                $data['venue_address'] = $booking['venue_address'];
                $data['notification_group'] = 'booking';
                $data['email'] = $booking['email'];
                $data['mobile'] = $booking['mobile'];
                $data['transaction_type'] = $booking['transaction_type'];
                $data['package_name'] = $booking['package_name'];
                $data['service_name'] = $booking['service_name'];
                $data['service_price'] = $booking['service_price'];
                $data['serviceAmount'] = $booking['serviceAmount'];
                // $data['services_total_amount'] = $data['services_total_amount'];
                $data['product_name'] = $booking['product_name'];
                $data['product_price'] = $booking['product_price'];
                $data['product_qty'] = $booking['product_qty'];
                $data['branch_name'] = $booking['branch_name'];
                $data['branch_number'] = $booking['branch_number'];
                $data['branch_email'] = $booking['branch_email'];
                // $data['product_amount'] = $data['product_amount'];
                $data['tip_amount'] = $booking['tip_amount'];
                $data['tax_amount'] = $booking['tax_amount'];
                $data['grand_total'] = $booking['grand_total'];
                $data['discount'] = $booking['coupon_discount'];

                $data['site_url'] = env('APP_URL');

                if ($data['type'] == 'complete_booking') {
                    $data['extra']['services'] = $booking['extra']['services'];
                    $data['extra']['products'] = $booking['extra']['products'];
                    $data['extra']['detail'] = $booking['extra']['detail'];
                }
                unset($data['booking']);
            } elseif (isset($order) && $order != null) {
                $data['notification_group'] = 'shop';
                $data['id'] = $order['id'];
                $data['user_id'] = $order['user_id'];
                $data['order_code'] = $order['order_code'];
                $data['user_name'] = $order['user_name'];
                $data['order_date'] = $order['order_date'];
                $data['order_time'] = $order['order_time'];
                $data['site_url'] = env('APP_URL');
                unset($data['order']);
            }elseif (isset($package) && $package != null) {
                // Package-specific logic
                $data['notification_group'] = 'package';
                $data['user_id'] = $data['user_id'];
                $data['user_name'] = $data['user_name'];
                $data['id'] = $package['id'];
                $data['package_name'] = $package['name']; 
                $data['package_expiry_date'] = $package['end_date'];
                $data['site_url'] = env('APP_URL');

                unset($data['package']);

            }

            switch ($mailTo) {
                case 'admin':

                    $admin = \App\Models\User::role('admin')->first();
                    $data['user_type'] = $admin->user_type;
                    if (isset($admin->email)) {
                        try {
                            $admin->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }

                    break;

                case 'manager':
                    if (isset($data['employee_id']) && $data['employee_id'] != '') {
                        $employee = \App\Models\User::find($data['employee_id']);
                        $data['user_type'] = $employee->user_type;
                        if (isset($employee->email)) {
                            try {
                                $employee->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                            } catch (\Exception $e) {
                                Log::error($e);
                            }
                        }
                    }

                    break;

                case 'user':
                    if (isset($data['user_id'])) {
                        $user = \App\Models\User::find($data['user_id']);
                        $data['user_type'] = $user->user_type;
                        try {
                            $user->notify(new \App\Notifications\CommonNotification($data['notification_type'], $data));
                        } catch (\Exception $e) {
                            Log::error($e);
                        }
                    }
                    break;
            }
        }
    }
}

function fcm($fields)
{
    $otherSetting = \App\Models\Setting::where('type', 'firebase_notification')->where('name', 'firebase_project_id')->first();
    $projectID = $otherSetting->val ?? null;
    $access_token = getAccessToken();
    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    $ch = curl_init('https://fcm.googleapis.com/v1/projects/' . $projectID . '/messages:send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $response = curl_exec($ch);
    Log::info($response);
    curl_close($ch);
}
function getAccessToken()
{
    $directory = storage_path('app/data');
    $credentialsFiles = File::glob($directory . '/*.json');
    if (empty($credentialsFiles)) {
        throw new Exception('No JSON credentials found in the specified directory.');
    }
    $client = new Google_Client();
    $client->setAuthConfig($credentialsFiles[0]);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    $token = $client->fetchAccessTokenWithAssertion();

    return $token['access_token'];
}

function timeAgoInt($date)
{
    if ($date == null) {
        return '-';
    }
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::parse($datetime)->diffInHours();

    return $diff_time;
}
function timeAgo($date)
{
    if ($date == null) {
        return '-';
    }
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::parse($datetime)->diffForHumans();

    return $diff_time;
}
function dateAgo($date, $type2 = '')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    $diff_time1 = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::parse($datetime->format('Y-m-d H:i:s'))->isoFormat('LLL');
    if ($type2 != '') {
        return $diff_time;
    }

    return $diff_time1 . ' on ' . $diff_time;
}

function customDate($date, $format = 'd-m-Y h:i A')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    $datetime = new \DateTime($date);
    // $la_time = new \DateTimeZone(\Auth::check() ? \Auth::user()->time_zone ?? 'UTC' : 'UTC');
    $la_time = new \DateTimeZone(setting('time_zone') ?? 'UTC');
    $datetime->setTimezone($la_time);
    $newDate = $datetime->format('Y-m-d H:i:s');
    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($newDate))->format($format);

    return $diff_time;
}

function saveDate($date)
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return null;
    }
    $datetime = new \DateTime($date);
    // $la_time = new \DateTimeZone(\Auth::check() ? \Auth::user()->time_zone ?? 'UTC' : 'UTC');
    $la_time = new \DateTimeZone(setting('time_zone') ?? 'UTC');
    $datetime->setTimezone($la_time);
    $newDate = $datetime->format('Y-m-d H:i:s');
    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($newDate));

    return $diff_time;
}
function strtotimeToDate($date)
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    $datetime = new \DateTime($date);
    $datetime->setTimezone(new \DateTimeZone(setting('time_zone') ?? 'UTC'));
    $diff_time = \Carbon\Carbon::createFromTimeStamp($datetime);

    return $diff_time;
}
function formatOffset($offset)
{
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }

    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
        . ':' . str_pad($minutes, 2, '0');
}

function timeZoneList()
{
    $list = \DateTimeZone::listAbbreviations();
    $idents = \DateTimeZone::listIdentifiers();

    $data = $offset = $added = [];
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (!empty($zone['timezone_id']) and !in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {
                $z = new \DateTimeZone($zone['timezone_id']);
                $c = new \DateTime(null, $z);
                $zone['time'] = $c->format('H:i a');
                $offset[] = $zone['offset'] = $z->getOffset($c);
                $data[] = $zone;
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_ASC, $data);
    $options = [];
    foreach ($data as $key => $row) {
        $options[$row['timezone_id']] = $row['time'] . ' - ' . formatOffset($row['offset']) . ' ' . $row['timezone_id'];
    }

    return $options;
}

/*
 * Global helpers file with misc functions.
 */
if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return setting('app_name') ?? config('app.name');
    }
}
/**
 * Avatar Find By Gender
 */
if (!function_exists('default_user_avatar')) {
    function default_user_avatar()
    {
        return asset(config('app.avatar_base_path') . 'avatar.png');
    }
    function default_user_name()
    {
        return env('APP_NAME').' User';
    }
}
if (!function_exists('user_avatar')) {
    function user_avatar()
    {
        if (auth()->user()->profile_image ?? null) {
            return auth()->user()->profile_image;
        } else {
            return asset(config('app.avatar_base_path') . 'avatar.png');
        }
    }
}

if (!function_exists('default_feature_image')) {
    function default_feature_image()
    {
        return asset(config('app.image_path') . 'default.png');
    }
}

if (!function_exists('product_feature_image')) {
    function product_feature_image()
    {
        return asset(config('app.image_path') . 'default2.png');
    }
}

if (!function_exists('promotion_image')) {
    function promotion_image()
    {
        return asset(config('app.image_path') . 'No_Image_Available.png');
    }
}
/*
 * Global helpers file with misc functions.
 */
if (!function_exists('user_registration')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function user_registration()
    {
        $user_registration = false;

        if (env('USER_REGISTRATION') == 'true') {
            $user_registration = true;
        }

        return $user_registration;
    }
}

/**
 * Global Json DD
 * !USAGE
 * return jdd($id);
 */
if (!function_exists('jdd')) {
    function jdd($data)
    {
        return response()->json($data, 500);
        exit();
    }
}

/*
 *
 * label_case
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('label_case')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function label_case($text)
    {
        $order = ['_', '-'];
        $replace = ' ';

        $new_text = trim(\Illuminate\Support\Str::title(str_replace('"', '', $text)));
        $new_text = trim(\Illuminate\Support\Str::title(str_replace($order, $replace, $text)));
        $new_text = preg_replace('!\s+!', ' ', $new_text);

        return $new_text;
    }
}

/*
 *
 * show_column_value
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('show_column_value')) {
    /**
     * Return Column values as Raw and formatted.
     *
     * @param  string  $valueObject  Model Object
     * @param  string  $column  Column Name
     * @param  string  $return_format  Return Type
     * @return string Raw/Formatted Column Value
     */
    function show_column_value($valueObject, $column, $return_format = '')
    {
        $column_name = $column->Field;
        $column_type = $column->Type;

        $value = $valueObject->$column_name;

        if ($return_format == 'raw') {
            return $value;
        }

        if (($column_type == 'date') && $value != '') {
            $datetime = \Carbon\Carbon::parse($value);

            return $datetime->isoFormat('LL');
        } elseif (($column_type == 'datetime' || $column_type == 'timestamp') && $value != '') {
            $datetime = \Carbon\Carbon::parse($value);

            return $datetime->isoFormat('LLLL');
        } elseif ($column_type == 'json') {
            $return_text = json_encode($value);
        } elseif ($column_type != 'json' && \Illuminate\Support\Str::endsWith(strtolower($value), ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
            $img_path = asset($value);

            $return_text = '<figure class="figure">
                                <a href="' . $img_path . '" data-lightbox="image-set" data-title="Path: ' . $value . '">
                                    <img src="' . $img_path . '" style="max-width:200px;" class="figure-img img-fluid rounded img-thumbnail" alt="">
                                </a>
                                <figcaption class="figure-caption">Path: ' . $value . '</figcaption>
                            </figure>';
        } else {
            $return_text = $value;
        }

        return $return_text;
    }
}

/*
 *
 * fielf_required
 * Show a * if field is required
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('fielf_required')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function fielf_required($required)
    {
        $return_text = '';

        if ($required != '') {
            $return_text = '<span class="text-danger">*</span>';
        }

        return $return_text;
    }
}

/*
 * Get or Set the Settings Values
 *
 * @var [type]
 */
if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        if (is_null($key)) {
            return new App\Models\Setting();
        }

        if (is_array($key)) {
            return App\Models\Setting::set($key[0], $key[1]);
        }

        $value = App\Models\Setting::get($key);

        return is_null($value) ? value($default) : $value;
    }
}

/*
 * Show Human readable file size
 *
 * @var [type]
 */
if (!function_exists('humanFilesize')) {
    function humanFilesize($size, $precision = 2)
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step = 1024;
        $i = 0;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }

        return round($size, $precision) . $units[$i];
    }
}

/*
 *
 * Encode Id to a Hashids\Hashids
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('encode_id')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function encode_id($id)
    {
        $hashids = new Hashids\Hashids(config('app.salt'), 3, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $hashid = $hashids->encode($id);

        return $hashid;
    }
}

/*
 *
 * Decode Id to a Hashids\Hashids
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('decode_id')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function decode_id($hashid)
    {
        $hashids = new Hashids\Hashids(config('app.salt'), 3, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $id = $hashids->decode($hashid);

        if (count($id)) {
            return $id[0];
        } else {
            abort(404);
        }
    }
}

/*
 *
 * Prepare a Slug for a given string
 * Laravel default str_slug does not work for Unicode
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('slug_format')) {
    /**
     * Format a string to Slug.
     */
    function slug_format($string)
    {
        $base_string = $string;

        $string = preg_replace('/\s+/u', '-', trim($string));
        $string = str_replace('/', '-', $string);
        $string = str_replace('\\', '-', $string);
        $string = strtolower($string);

        $slug_string = $string;

        return $slug_string;
    }
}

/*
 *
 * icon
 * A short and easy way to show icon fornts
 * Default value will be check icon from FontAwesome
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('icon')) {
    /**
     * Format a string to Slug.
     */
    function icon($string = 'fas fa-check')
    {
        $return_string = "<i class='" . $string . "'></i>";

        return $return_string;
    }
}

/*
 *
 * Decode Id to a Hashids\Hashids
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('generate_rgb_code')) {
    /**
     * Prepare the Column Name for Lables.
     */
    function generate_rgb_code($opacity = '0.9')
    {
        $str = '';
        for ($i = 1; $i <= 3; $i++) {
            $num = mt_rand(0, 255);
            $str .= "$num,";
        }
        $str .= "$opacity,";
        $str = substr($str, 0, -1);

        return $str;
    }
}

/*
 *
 * Return Date with weekday
 *
 * ------------------------------------------------------------------------
 */
if (!function_exists('date_today')) {
    /**
     * Return Date with weekday.
     *
     * Carbon Locale will be considered here
     * Example:
     * Friday, July 24, 2020
     */
    function date_today()
    {
        $str = \Carbon\Carbon::now()->isoFormat('dddd, LL');

        return $str;
    }
}

if (!function_exists('language_direction')) {
    /**
     * return direction of languages.
     *
     * @return string
     */
    function language_direction($language = null)
    {
        if (empty($language)) {
            $language = app()->getLocale();
        }
        $language = strtolower(substr($language, 0, 2));
        $rtlLanguages = [
            'ar', //  'العربية', Arabic
            'arc', //  'ܐܪܡܝܐ', Aramaic
            'bcc', //  'بلوچی مکرانی', Southern Balochi
            'bqi', //  'بختياري', Bakthiari
            'ckb', //  'Soranî / کوردی', Sorani Kurdish
            'dv', //  'ދިވެހިބަސް', Dhivehi
            'fa', //  'فارسی', Persian
            'glk', //  'گیلکی', Gilaki
            'he', //  'עברית', Hebrew
            'lrc', //- 'لوری', Northern Luri
            'mzn', //  'مازِرونی', Mazanderani
            'pnb', //  'پنجابی', Western Punjabi
            'ps', //  'پښتو', Pashto
            'sd', //  'سنڌي', Sindhi
            'ug', //  'Uyghurche / ئۇيغۇرچە', Uyghur
            'ur', //  'اردو', Urdu
            'yi', //  'ייִדיש', Yiddish
        ];
        if (in_array($language, $rtlLanguages)) {
            return 'rtl';
        }

        return 'ltr';
    }
}

if (!function_exists('module_exist')) {
    /**
     * return value for module exist or not.
     *
     * @return bool
     */
    function module_exist($module_name)
    {
        return \Module::find($module_name)?->isEnabled() ?? false;
    }
}

function storeMediaFile($module, $file, $key = 'feature_image')
{
    if (isset($module) && isset($file)) {
        $module->clearMediaCollection($key);
        $mediaItems = $module->addMedia($file)->toMediaCollection($key);
    }

    if ($file == '') {
        $module->clearMediaCollection($key);
    }
}
function getCustomizationSetting($name, $key = 'customization_json')
{
    $settingObject = setting($key);
    if (isset($settingObject) && $key == 'customization_json') {
        try {
            $settings = (array) json_decode(html_entity_decode(stripslashes($settingObject)))->setting;

            return collect($settings[$name])['value'];
        } catch (\Exception $e) {
            return '';
        }

        return '';
    } elseif ($key == 'root_color') {
        //
    }

    return '';
}
// Usage: getCustomizationSetting('app_name') it will return value of this json
// getCustomizationSetting('footer')
// getCustomizationSetting('menu_style)

function str_slug($title, $separator = '-', $language = 'en')
{
    return Str::slug($title, $separator, $language);
}

function formatCurrency($number, $noOfDecimal, $decimalSeparator, $thousandSeparator, $currencyPosition, $currencySymbol)
{
    $number = floatval($number);

    // Convert the number to a string with the desired decimal places
    $formattedNumber = number_format($number, $noOfDecimal, '.', '');

    // Split the number into integer and decimal parts
    $parts = explode('.', $formattedNumber);
    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '';

    // Add thousand separators to the integer part
    $integerPart = number_format($integerPart, 0, '', $thousandSeparator);

    // Construct the final formatted currency string
    $currencyString = '';

    if ($currencyPosition == 'left' || $currencyPosition == 'left_with_space') {
        $currencyString .= $currencySymbol;
        if ($currencyPosition == 'left_with_space') {
            $currencyString .= ' ';
        }
        $currencyString .= $integerPart;
        // Add decimal part and decimal separator if applicable
        if ($noOfDecimal > 0) {
            $currencyString .= $decimalSeparator . $decimalPart;
        }
    }

    if ($currencyPosition == 'right' || $currencyPosition == 'right_with_space') {
        // Add decimal part and decimal separator if applicable
        if ($noOfDecimal > 0) {
            $currencyString .= $integerPart . $decimalSeparator . $decimalPart;
        }
        if ($currencyPosition == 'right_with_space') {
            $currencyString .= ' ';
        }
        $currencyString .= $currencySymbol;
    }

    return $currencyString;
}

function timeAgoFormate($date)
{
    if ($date == null) {
        return '-';
    }

    // date_default_timezone_set('UTC');

    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();

    return $diff_time;
}

function getDiscountedProductPrice($product_price, $product_id)
{
    $product = \Modules\Product\Models\Product::where('id', $product_id)->first();

    $discount_applicable = false;

    if ($product != null) {
        if (
            $product->discount_start_date !== null &&
            $product->discount_end_date !== null &&
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            $discount_type = $product['discount_type'];
            $discount_value = $product['discount_value'];

            $discount_amount = 0;

            if ($discount_type == 'percent') {
                $discount_amount += $product_price * $discount_value / 100;
            } elseif ($discount_type == 'fixed') {
                $discount_amount += $discount_value;
            }

            return $product_price - $discount_amount;
        }

        return $product_price;
    } else {
        return 0;
    }
}

function checkInWishList($product_id, $user_id)
{
    $product = \Modules\Product\Models\WishList::where('product_id', $product_id)->where('user_id', $user_id)->first();

    if (!$product) {
        return 0;
    } else {
        return 1;
    }
}

function checkInCart($product_variation_id, $user_id)
{
    $cart = \Modules\Product\Models\Cart::where('user_id', $user_id)->where('product_variation_id', $product_variation_id)->first();

    if (!$cart) {
        return 0;
    } else {
        return 1;
    }
}

function checkIsLike($review_id, $user_id)
{
    $review = \Modules\Product\Models\Review::find($review_id);

    if (!$review) {
        return 0; // Review not found
    }

    $isLiked = $review->likes()
        ->where('user_id', $user_id)
        ->where('is_like', 1)
        ->exists();

    return $isLiked ? 1 : 0;
}

function checkIsdisLike($review_id, $user_id)
{
    $review = \Modules\Product\Models\Review::find($review_id);

    if (!$review) {
        return 0; // Review not found
    }

    $isLiked = $review->likes()
        ->where('user_id', $user_id)
        ->where('dislike_like', 1)
        ->exists();

    return $isLiked ? 1 : 0;
}

function getDiscountedPrice($data)
{
    $sumOfDiscountedPrices = 0;

    if ($data) {
        foreach ($data as $cart) {
            $price = $cart->product_variation->price;

            $discount_applicable = false;

            if (
                $cart->product->discount_start_date !== null &&
                $cart->product->discount_end_date !== null &&
                strtotime(date('d-m-Y H:i:s')) >= $cart->product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $cart->product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($cart->product->discount_type === 'percent') {
                    $discountedPrice = ($price * $cart->product->discount_value) / 100;
                } elseif ($cart->product->discount_type === 'fixed') {
                    $discountedPrice = $cart->product->discount_value;
                }

                $sumOfDiscountedPrices += $discountedPrice;
            }
        }
    }

    return $sumOfDiscountedPrices;
}

if (!function_exists('variationDiscountedPrice')) {
    // return discounted price of a variation
    function variationDiscountedPrice($product, $variation, $addTax = false)
    {
        $price = $variation->price;

        $discount_applicable = false;

        if ($product->discount_start_date == null || $product->discount_end_date == null) {
            $discount_applicable = false;
        } elseif (
            strtotime(date('d-m-Y ')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount_value) / 100;
            } elseif ($product->discount_type == 'fixed') {
                $price -= $product->discount_value;
            }
        }

        if ($addTax) {
            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $price += ($price * $product_tax->tax_value) / 100;
                } elseif ($product_tax->tax_type == 'fixed') {
                    $price += $product_tax->tax_value;
                }
            }
        }

        return $price;
    }
}

function getDiscountAmount($data)
{
    $sumOfDiscountedPrices = 0;

    if ($data) {
        foreach ($data as $cart) {
            if ($cart->product) {
                $price = $cart->product_variation->price * $cart->qty;

                $discount_applicable = false;

                if ($cart->product->discount_start_date == null || $cart->product->discount_end_date == null) {
                    $discount_applicable = false;
                } elseif (
                    strtotime(date('d-m-Y')) >= $cart->product->discount_start_date &&
                    strtotime(date('d-m-Y')) <= $cart->product->discount_end_date
                ) {
                    $discount_applicable = true;
                }

                if ($discount_applicable) {
                    if ($cart->product->discount_type === 'percent') {
                        $discountedPrice = ($price * $cart->product->discount_value) / 100;
                    } elseif ($cart->product->discount_type === 'fixed') {
                        $discountedPrice = $cart->product->discount_value;
                    }

                    $sumOfDiscountedPrices += $discountedPrice;
                }
            }
        }
    }

    return $sumOfDiscountedPrices;
}

if (!function_exists('getSubTotal')) {
    // return sub total price
    function getSubTotal($carts, $couponDiscount = true, $couponCode = '', $addTax = true)
    {
        $price = 0;
        $amount = 0;
        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $product = $cart->product_variation->product;
                $variation = $cart->product_variation;

                $discountedVariationPriceWithTax = variationDiscountedPrice($product, $variation, $addTax);
                $price += (float) $discountedVariationPriceWithTax * $cart->qty;
            }
        }

        return $price - $amount;
    }
}

if (!function_exists('generateVariationOptions')) {
    //  generate combinations based on variations
    function generateVariationOptions($options)
    {
        if (count($options) == 0) {
            return $options;
        }
        $variation_ids = [];
        foreach ($options as $option) {
            $value_ids = [];
            if (isset($variation_ids[$option->variation_id])) {
                $value_ids = $variation_ids[$option->variation_id];
            }
            if (!in_array($option->variation_value_id, $value_ids)) {
                array_push($value_ids, $option->variation_value_id);
            }
            $variation_ids[$option->variation_id] = $value_ids;
        }
        $options = [];
        foreach ($variation_ids as $id => $values) {
            $variationValues = [];
            foreach ($values as $value) {
                $variationValue = \Modules\Product\Models\VariationValue::find($value);
                $val = [
                    'id' => $value,
                    'name' => $variationValue->name,
                ];
                array_push($variationValues, $val);
            }
            $variation = \Modules\Product\Models\Variations::find($id);
            if ($variation) {
                $data['id'] = $id;
                $data['name'] = $variation->name;
                $data['values'] = $variationValues;

                array_push($options, $data);
            }
        }

        return $options;
    }
}

function getproductDiscountAmount($data)
{
    $sumOfDiscountedPrices = 0;

    if ($data) {
        foreach ($data as $cart) {
            $price = $cart->product_price * $cart->product_qty;

            $discount_applicable = false;

            if ($cart->product->discount_start_date == null || $cart->product->discount_end_date == null) {
                $discount_applicable = false;
            } elseif (
                strtotime(date('d-m-Y')) >= $cart->product->discount_start_date &&
                strtotime(date('d-m-Y')) <= $cart->product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($cart->product->discount_type === 'percent') {
                    $discountedPrice = ($price * $cart->product->discount_value) / 100;
                } elseif ($cart->product->discount_type === 'fixed') {
                    $discountedPrice = $cart->product->discount_value;
                }

                $sumOfDiscountedPrices += $discountedPrice;
            }
        }
    }

    return $sumOfDiscountedPrices;
}

function getTaxamount($amount)
{

    $tax_list = \Modules\Tax\Models\Tax::where('status', 1)->where('module_type', 'products')->get();

    $total_tax_amount = 0;
    $tax_details = [];
    $tax_amount = 0;

    foreach ($tax_list as $tax) {
        if ($tax->type == 'percent') {
            $tax_amount = $amount * $tax->value / 100;
        } elseif ($tax->type == 'fixed') {
            $tax_amount = $tax->value;
        }

        $tax_details[] = [
            'tax_name' => $tax->title,
            'tax_type' => $tax->type,
            'tax_value' => $tax->value,
            'tax_amount' => $tax_amount,
        ];

        $total_tax_amount += $tax_amount;
    }

    return [
        'total_tax_amount' => $total_tax_amount,
        'tax_details' => $tax_details,
    ];
}

function getBookingTaxamount($amount, $couponAmount, $tax_data)
{
    $tax_list = $tax_data;

    $total_tax_amount = 0;
    $tax_details = [];
    $tax_amount = 0;
    $amt = $amount - $couponAmount;
    if ($tax_list != null) {

        foreach ($tax_list as $tax) {

            if (is_array($tax) && isset($tax['type'])) {

                if ($tax['type'] == 'percent') {
                    $tax_amount = $amt * $tax['percent'] / 100;
                } elseif ($tax['type'] == 'fixed') {
                    $tax_amount = $tax['tax_amount'];
                }

                $tax_details[] = [
                    'tax_name' => $tax['name'],
                    'tax_type' => $tax['type'],
                    'tax_value' => isset($tax['percent']) ? $tax['percent'] : $tax['tax_amount'],
                    'tax_amount' => $tax_amount,
                ];

                $total_tax_amount += $tax_amount;
            }
        }
    } else {

        $tax_list = \Modules\Tax\Models\Tax::active()
            ->whereNull('module_type')
            ->orWhere('module_type', 'services')->where('status', 1)->get();

        foreach ($tax_list as $tax) {

            if ($tax['type'] == 'percent') {
                $tax_amount = $amt * $tax['value'] / 100;
            } elseif ($tax['type'] == 'fixed') {
                $tax_amount = $tax['value'];
            }

            $tax_details[] = [
                'tax_name' => $tax['title'],
                'tax_type' => $tax['type'],
                'tax_value' => isset($tax['percent']) ? $tax['percent'] : $tax['value'],
                'tax_amount' => $tax_amount,
            ];

            $total_tax_amount += $tax_amount;
        }
    }

    return [
        'total_tax_amount' => $total_tax_amount,
        'tax_details' => $tax_details,
    ];
}




if (!function_exists('applyExcelStyles')) {
    /**
     * Apply common styles to an Excel worksheet.
     *
     * @param Worksheet $sheet
     * @return void
     */
    function applyExcelStyles(Worksheet $sheet)
    {
        // Determine the data size
        $rowCount = $sheet->getHighestRow();
        $columnCount = $sheet->getHighestColumn();
        $totalColumns = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnCount);

        // Set column width based on the number of columns
        foreach (range('A', $columnCount) as $col) {
            // Default width
            $width = 10;

            // Example logic to adjust column width
            if ($totalColumns <= 3) {
                $width = 22;
            } else if ($totalColumns <= 5) {
                $width = 15; // Wider columns for fewer columns
            } else if ($totalColumns <= 7) {
                $width = 12; // Wider columns for fewer columns
            } else if ($totalColumns <= 10) {
                $width = 10; // Wider columns for fewer columns
            } elseif ($totalColumns <= 20) {
                $width = 8; // Medium width for moderate number of columns
            }

            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->getStyle("A1:{$columnCount}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            ],
        ]);

        // Center-align all cells
        $sheet->getStyle("A1:{$columnCount}{$rowCount}")
            ->getAlignment()->setHorizontal('center')
            ->setVertical('center');

        // Add thin borders to all cells
        $sheet->getStyle("A1:{$columnCount}{$rowCount}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Set row height
        for ($row = 1; $row <= $rowCount; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(25);  // Adjust as needed
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Set up page settings for portrait orientation
        $pageSetup = $sheet->getPageSetup();

        // Set page size (A4 or Letter)
        $pageSetup->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4); // or PAPERSIZE_LETTER

        // Scale content to fit the page width
        $pageSetup->setFitToWidth(1);  // Fit all columns to one page width
        $pageSetup->setFitToHeight(0); // No limit on height, allowing it to span multiple pages vertically

        $sheet->getPageMargins()->setTop(0.5)
            ->setBottom(0.5)
            ->setLeft(0.5)
            ->setRight(0.5);

        // Optionally set print area
        $sheet->getPageSetup()->setPrintArea("A1:{$columnCount}{$rowCount}");
    }

    if (!function_exists('datatableTranslations')) {
        function datatableTranslations()
        {
            return [
                'processing' => __('messages.processing'),
                'search' => __('messages.search'),
                'lengthMenu' => __('messages.lengthMenu'),
                'info' => __('messages.info'),
                'infoEmpty' => __('messages.infoEmpty'),
                'infoFiltered' => __('messages.infoFiltered'),
                'loadingRecords' => __('messages.loadingRecords'),
                'zeroRecords' => __('messages.zeroRecords'),
                'paginate' => [
                    'first' => __('messages.paginate.first'),
                    'last' => __('messages.paginate.last'),
                    'next' => __('messages.paginate.next'),
                    'previous' => __('messages.paginate.previous'),
                ]
            ];
        }
    }
}

function dbConnectionStatus(): bool
{
    try {
        DB::connection()->getPdo();
    return true;
    } catch (Exception $e) {
        return false;
    }
}

if (!function_exists('isActive')) {
    /**
     * Returns 'active' or 'done' class based on the current step.
     *
     * @param  string|array  $route
     * @param  string  $className
     * @return string
     */
    function isActive($route, $className = 'active') {
        $currentRoute = Route::currentRouteName();

        if (is_array($route)) {
            return in_array($currentRoute, $route) ? $className : '';
        }

        return $currentRoute == $route ? $className : '';
    }
}


