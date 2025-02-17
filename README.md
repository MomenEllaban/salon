# Salon Web Admin App (Laravel 9.2)

#### Run Below Command For PHP Code Styling
- "pre-commit": "./vendor/bin/pint"
## Setup Steps
- ``cp .env.example .env``
- ``composer install``
- ``npm install``
- ``npm run prod`` or ``npm run dev``
- ``APP_URL=http://127.0.0.1`` || ``MIX_ASSET_URL=http://127.0.0.1`` path should be change in .env

## Database Setup
- configure db config in .env file 
    - ``DB_DATABASE=salon``
    - ``DB_USERNAME``
    - ``DB_PASSWORD`` 
- Run `php artisan migrate:fresh --seed` command for add all tables to your database
##### Seprate Command
- ``php artisan migrate``
- ``php artisan db:seed`` (get error when data already inserted)
- ``php artisan migrate:fresh`` (existing database fresh table)

After Setup DB then run below project run command
## Project Run
- ``php artisan serve``

## Features
- Branch Based
- Services
- Subscription Module
- Product Inventory Feature Used With Bagisto
- Fork Starter Kit By nasirkhan/laravel-starter
- Design System Used With Hope UI Pro
- Font Awsome Icon 
- Moduler Code
- Api Based All Modules
- Test Cases
- Setup Wizard
- Vue Based Form In Frontent
- React Based Future Plan

## (Saas System)
- Company Based Setting
- Company Based Login
- Company Based All tables & there recoreds

## Figma
- DFD Diagram
- ERD Diagram
- App Design
- Typography

## Plugins
- Datatable
- Flatpicker
- Bootstrap
- Vue
    - Vee Validate
    - Pinia
    - Vue Store



## Calender

eventSources: [{events: function() {
    console.log('fetching...');
    return [];
}}]

function invokeMethod() {
    ec.refetchEvents();
}

onClick="invokeMethod"

removeEventById( id )
addEvent( event )
updateEvent( event )
{
    id: 1,
    resourceIds: 1,
    start: 00:00,
    end: 00:00,
    title: 'Event Title 1',
    editable: true,
    backgroundColor: '',
    color: '',
    extendedProps: {}
}


## Resources When You Are creating api
https://laravel.com/docs/9.x/eloquent-resources

## Module Generator

https://github.com/nasirkhan/laravel-starter
php artisan module:build NotificationTemplates

## Single File Creation
https://docs.laravelmodules.com/v9/artisan-commands

## How to use store
// Store
import { useCounterStore } from  '../store/booking'
const store = useCounterStore()

const doubleValue = computed(() => store.doubleCount)

setInterval(() => {
  store.increment()
}, 500);


Translation In PHP File
__('save_form')

__('constant.title']

Blade

@lang('are_you_sure?') change to {{ __('constant.title') }}

Vue
$t('constant.lbl_type')

vue props
:label="$t('constant.lbl_type')"

{{ $t('constant.lbl_type') }}



"husky": {
        "hooks": {
            "pre-push": "php artisan test"
        }
    }
