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
        'Feed',
        'Medicine',
        'Equipment',
        'Fertilizer',
        'Seed',
        'Roe',
        'Livestock',
        'Pesticide',
        'Transportation',
    ];

    /**
     * @throws \Exception
     */
    public static function getSupplierProducts(string $type): array {
        return match ($type) {
            'Feed' => [self::$feedProducts, 'Kg'],
            'Medicine' => [self::$medicineProducts, 'gram'],
            'Equipment' => [self::$equipmentProducts, 'Unit'],
            'Fertilizer' => [self::$fertilizerProducts, 'Kg'],
            'Seed' => [self::$seedProducts, 'gram'],
            'Roe' => [self::$roeProducts, 'gram'],
            'Livestock' => [self::$livestockProducts, 'Unit'],
            'Pesticide' => [self::$pesticideProducts, 'Kg'],
            'Transportation' => [self::$transportationProducts, 'Unit'],
            default => throw new \Exception("Invalid Supplier Type: " . $type),
        };
    }

    // Feed
    public static array $feedProducts = [
        "Premium Poultry Feed",
        "Organic Cattle Feed",
        "Aquaculture Fish Feed",
    ];

// Medicine
    public static array $medicineProducts = [
        "Veterinary Antibiotics",
        "Livestock Vaccines",
        "Poultry Health Supplements",
        "Fish Disease Treatment",
    ];

// Equipment
    public static array $equipmentProducts = [
        "Tractor",
        "Watering Trough",
        "Harvester",
        "Water Pump",
        "Power Tiller",
        "Fencing",
        "Feed Trough",
        "Aerator",
        "Water Filtration",
        "Net",
        "Basket",
        "Hoe",
        "Shovel",
        "Sickle"
    ];

// Fertilizer
    public static array $fertilizerProducts = [
        "Organic Compost Fertilizer",
        "Nitrogen-Rich Plant Food",
        "Phosphorus Enriched Soil Amendment",
        "Potash-Packed Garden Fertilizer",
        "Liquid Seaweed Fertilizer",
    ];

// Seed
    public static array $seedProducts = [
        "Binatomato 11 Seeds",
        "BRII 87 Rice Seeds",
        "BWMRI 4 Wheat Seeds"
    ];

// Roe
    public static array $roeProducts = [
        "Silver Carp Fish Eggs",
        "Rui Roe",
        "Catla Roe",
    ];

// Livestock
    public static array $livestockProducts = [
        "Beef Cattle",
        "Dairy Calf",
        "Broiler Chicks",
    ];

// Pesticide
    public static array $pesticideProducts = [
        "Organic Insect Repellent",
        "Herbicide Spray",
        "Crop Protection Chemicals",
        "Rodent Control Bait",
        "Fungicide Solution",
    ];

// Transportation
    public static array $transportationProducts = [
        "Livestock Trailer",
        "Delivery Truck",
        "ATV for Fieldwork",
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
