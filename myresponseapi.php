$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://apis.estated.com/v4/property?token=82rzrShdRgnuXwf8tB2PGJnH7XhLSe&amp;street_address=210+Carneros+Ave+Aromas%2C+California%28CA%29&amp;city=California&amp;state=CA&amp;zip_code=95004");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$properties = curl_exec($ch);

echo $properties;


{
    "data": {
        "metadata": {
            "publishing_date": "2020-07-01"
        },
        "address": {
            "street_number": "210",
            "street_pre_direction": null,
            "street_name": "CARNEROS",
            "street_suffix": "AVE",
            "street_post_direction": null,
            "unit_type": null,
            "unit_number": null,
            "formatted_street_address": "210 CARNEROS AVE",
            "city": "AROMAS",
            "state": "CA",
            "zip_code": "95004",
            "zip_plus_four_code": "9717",
            "carrier_code": "R010",
            "latitude": 36.869473,
            "longitude": -121.649227,
            "geocoding_accuracy": "PARCEL CENTROID",
            "census_tract": "060530101.022011"
        },
        "parcel": {
            "apn_original": "141-121-042-000",
            "apn_unformatted": "141121042000",
            "apn_previous": null,
            "fips_code": "06053",
            "frontage_ft": null,
            "depth_ft": null,
            "area_sq_ft": 113256,
            "area_acres": 2.6,
            "county_name": "MONTEREY",
            "county_land_use_code": "3E",
            "county_land_use_description": "RES IMP SITE TO 10 ACRES",
            "standardized_land_use_category": "RESIDENTIAL",
            "standardized_land_use_type": "RURAL OR AGRICULTURAL RESIDENCE",
            "location_descriptions": [],
            "zoning": null,
            "building_count": null,
            "tax_account_number": null,
            "legal_description": "VOL 5 PAR MAPS PG 64 PAR 2 2.6 AC",
            "lot_code": null,
            "lot_number": null,
            "subdivision": null,
            "municipality": null,
            "section_township_range": null
        },
        "structure": {
            "year_built": 1900,
            "effective_year_built": 2001,
            "stories": "1",
            "rooms_count": null,
            "beds_count": null,
            "baths": null,
            "partial_baths_count": null,
            "units_count": null,
            "parking_type": null,
            "parking_spaces_count": null,
            "pool_type": null,
            "architecture_type": null,
            "construction_type": null,
            "exterior_wall_type": null,
            "foundation_type": null,
            "roof_material_type": null,
            "roof_style_type": null,
            "heating_type": null,
            "heating_fuel_type": null,
            "air_conditioning_type": null,
            "fireplaces": "YES",
            "basement_type": null,
            "quality": "C",
            "condition": null,
            "flooring_types": [],
            "plumbing_fixtures_count": null,
            "interior_wall_type": null,
            "water_type": null,
            "sewer_type": null,
            "total_area_sq_ft": 2259,
            "other_areas": [],
            "other_rooms": [],
            "other_features": [],
            "other_improvements": [],
            "amenities": []
        },
        "valuation": {
            "value": 690000,
            "high": 848700,
            "low": 531300,
            "forecast_standard_deviation": 23,
            "date": "2020-12-02"
        },
        "taxes": [
            {
                "year": 2020,
                "amount": 4982,
                "exemptions": [],
                "rate_code_area": "52-022"
            }
        ],
        "assessments": [
            {
                "year": 2020,
                "land_value": 81928,
                "improvement_value": 365479,
                "total_value": 447407
            },
            {
                "year": 2019,
                "land_value": 80322,
                "improvement_value": 358313,
                "total_value": 438635
            },
            {
                "year": 2018,
                "land_value": 78748,
                "improvement_value": 351288,
                "total_value": 430036
            }
        ],
        "market_assessments": [],
        "owner": {
            "name": "DEHN, DAVID A; DEHN, KATHERINE L",
            "second_name": null,
            "unit_type": null,
            "unit_number": null,
            "formatted_street_address": "210 CARNEROS AVE",
            "city": "AROMAS",
            "state": "CA",
            "zip_code": "95004",
            "zip_plus_four_code": "9717",
            "owner_occupied": "YES"
        },
        "deeds": [
            {
                "document_type": "INTRAFAMILY TRANSFER AND DISSOLUTION",
                "recording_date": "2019-04-15",
                "original_contract_date": "2019-03-19",
                "deed_book": null,
                "deed_page": null,
                "document_id": "2019014533",
                "sale_price": null,
                "sale_price_description": "TRANSFER TAX ON DOCUMENT INDICATED AS EXEMPT",
                "transfer_tax": null,
                "distressed_sale": false,
                "real_estate_owned": "NO",
                "seller_first_name": "DAVID ALLAN",
                "seller_last_name": "DEHN",
                "seller2_first_name": "KATHERINE LARAE",
                "seller2_last_name": "DEHN",
                "seller_address": null,
                "seller_unit_number": null,
                "seller_city": null,
                "seller_state": null,
                "seller_zip_code": "00000",
                "seller_zip_plus_four_code": "0000",
                "buyer_first_name": "DAVID A",
                "buyer_last_name": "DEHN",
                "buyer2_first_name": "KATHERINE L",
                "buyer2_last_name": "DEHN",
                "buyer_address": "210 CARNEROS AVE",
                "buyer_unit_type": null,
                "buyer_unit_number": null,
                "buyer_city": "AROMAS",
                "buyer_state": "CA",
                "buyer_zip_code": "95004",
                "buyer_zip_plus_four_code": "9717",
                "lender_name": null,
                "lender_type": null,
                "loan_amount": null,
                "loan_type": null,
                "loan_due_date": null,
                "loan_finance_type": null,
                "loan_interest_rate": null
            }
        ]
    },
    "metadata": {
        "timestamp": "2020-12-17T11:05:15.893454Z",
        "version": "0.8.12-2.6.0"
    },
    "warnings": []
}
