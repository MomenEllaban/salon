<?php

use Modules\Logistic\Http\Controllers\Backend\API\LogisticZoneController;

Route::get('get-logisticzone-list', [LogisticZoneController::class, 'logisticzoneList']);
