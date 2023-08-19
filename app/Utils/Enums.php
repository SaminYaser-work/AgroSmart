<?php

namespace App\Utils;

class Enums
{
    public static array $StorageType = ['Barn', 'Warehouse', 'Shed', 'Silo',];

    public static array $Units = ['kg', 'litre', 'unit', 'hectare', 'acre',];

    public static array $AnimalType = ['Cow', 'Chicken', 'Sheep', 'Goat',];

    public static array $Gender = ['Male', 'Female', 'Castrated'];

    public static array $CowBreed = ['Holstein', 'Jersey', 'Brown Swiss', 'Guernsey', 'Ayrshire', 'Milking Shorthorn', 'Red Sindhi', 'Sahiwal', 'Tharparkar', 'Gir', 'Kankrej', 'Ongole', 'Deoni', 'Rathi', 'Harayana', 'Khillari', 'Nagori', 'Red Kandhari', 'Umblachery', 'Vechur'];

    public static array $WorkerDesignations = ['Farm Hand', 'Farm Manager', 'Farm Supervisor', 'Crop Specialist', 'Irrigation Technician', 'Livestock Supervisor', 'Fishery Manager', 'Farm Equipment Technician', 'Farm Equipment Operator', 'Animal Health Technician', 'Harvesting Crew', 'Dairy Farm Worker', 'Veterinarian',];

    public static array $SupplierType = [
        'Feed Supplier',
        'Medicine Supplier',
        'Equipment Supplier',
        'Fertilizer Supplier',
        'Seed Supplier',
        'Pesticide Supplier',
        'Fuel Supplier',
        'Water Supplier',
        'Electricity Supplier',
        'Labor Supplier',
        'Transportation Supplier',
        'Other Supplier',
    ];

    public static array $SoilType = [
        'Clay',
        'Sandy',
        'Silty',
        'Peaty',
        'Chalky',
        'Loamy',
    ];

    public static array $FieldStatus = [
        true, false
    ];

    public static array $AnimalExpenseType = [
        'Feed',
        'Supplements',
        'Medicine',
        'Hygiene/Sanitation',
    ];

    public static array $ItemType = [
        'Dairy',
        'Fishery',
        'Crop',
    ];

    public static array $CropName = [
        'Rice',
        'Wheat',
        'Tomato'
    ];

    public static array $GrainWeight = [
        'Rice' => 0.02,
        'Wheat' => 3.4,
    ];

    # http://knowledgebank-brri.org/wp-content/uploads/2014/02/BRRI-dhan87-1.pdf
    public static float $RiceYieldTonPerHa = 6.50;
    public static float $RiceLifeCycleInDays = 127;

    # http://www.bwmri.gov.bd/site/page/0dc71a49-5639-4d36-b42f-3172a03c5543/-
    public static float $WheatYieldTonPerHa = 4.75;
    public static float $WheatLifeCycleInDays = 107;

    # https://bina.portal.gov.bd/sites/default/files/files/bina.portal.gov.bd/page/2fe324dc_6d39_455c_b522_15175672b318/Tomato%20Varieties.pdf
    public static float $TomatoYieldTonPerHa = 82.5;
    public static float $TomatoLifeCycleInDays = 111;

    public static function getExpectedYield(string $cropName): float {
        return match ($cropName) {
            'Rice' => self::$RiceYieldTonPerHa,
            'Wheat' => self::$WheatYieldTonPerHa,
            'Tomato' => self::$TomatoYieldTonPerHa,
            default => 0,
        };
    }

    public static function getExpectedEndDateInDays(string $cropName): float {
        return match ($cropName) {
            'Rice' => self::$RiceLifeCycleInDays,
            'Wheat' => self::$WheatLifeCycleInDays,
            'Tomato' => self::$TomatoLifeCycleInDays,
            default => 0,
        };
    }

    public static array $FishName = [
        'Silver Carp',
        'Rui',
        'Catla',
    ];

    public static array $AnimalProductName = [
        "Milk"
    ];

    public static array $SaleItem = [
        'Milk',
        'Silver Carp',
        'Rui',
        'Catla',
        'Rice',
        'Wheat',
        'Tomato'
    ];

    public static array $CropStage = [
        'Soil Preparation',
        'Sowing',
        'Growing',
        'Harvesting',
        'Stored'
    ];
}
