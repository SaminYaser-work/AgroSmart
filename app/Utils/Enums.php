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
