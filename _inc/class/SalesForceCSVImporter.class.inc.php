<?php

class SalesForceCSVImporter
{
    public $company_fieldsArr = array(
        'ID' => 'Id',
        'NAME' => 'Name',
        'TYPE' => 'Type',
        'HEADLINE__C' => 'Headline__c',
        'APP_ABOUT_US__C' => 'App_About_Us__c',
        'WEBSITE' => 'Website',
        'LINKEDIN__C' => 'LinkedIn__c',
        'MONTHLY_MEETING_ROOM_ALLOWANCE__C' => 'Monthly_Meeting_Room_Allowance__c',
        'PR_OPT_OUT__C' => 'PR_Opt_Out__c',
    );

    public $company_rooms_fieldsArr = array(
        'ID' => 'Id',
        'Product Name' => 'Product_Name__c',
        'Product Family' => 'Product_Family__c',
        'Status' => 'Status__c',
        'Site' => 'Product_Site__c',
        'Floor' => 'Product_Floor__c',
        'Room #' => 'Product_Room__c',
        'End Time' => 'End_Time__c',
        'Account Id field' => 'AccountId',
    );

    public $user_fieldsArr = array(
        'ID' => 'Id',
        'FIRSTNAME' => 'FirstName',
        'LASTNAME' => 'LastName',
        'ACCOUNTID' => 'AccountId',
        'TYPE__C' => 'Type__c',
        'EMAIL' => 'Email',
        'MOBILEPHONE' => 'MobilePhone',
        'PRIMARY_CONTACT__C' => 'Primary_Contact__c',
        'IS_ALLOWED_TO_BOOK_MEETING_ROOMS__C' => 'Is_Allowed_To_Book_Meeting_Rooms__c',
        'APP_ADMIN__C' => 'App_Admin__c',
        'GENDER__C' => 'Gender__c',
        'TITLE' => 'Title',
        'Status' => 'Status__c',
    );

    public $meeting_room_fieldsArr = array(
        'ID' => 'Id',
        'Product Name' => 'Name',
        'Site' => 'Site__c',
        'Room #' => 'Room__c',
        'Floor' => 'Floor__c',
        'Zone' => 'Zone__c',
        'Product Code' => 'ProductCode',
        'Monthly Meeting Room Allowance (hours)' => 'Monthly_Meeting_Room_Allowance_hours__c',
        'Monthly Page Prints Allowance' => 'Monthly_Page_Prints_Allowance__c',
        'No. WS' => 'Size_Workstations__c',
        'CORNER_OFFICE__C' => 'Corner_Office__c',
        'With Window' => 'With_Window__c',
    );

    public function get_csvArr($file_name, $fieldsArr)
    {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/_import/' . $file_name . '.csv';
        if (!file_exists($file_path)) {
            return false;
        }

        if (($handle = fopen($file_path, 'r')) !== false) {
            $csvArr = array();
            $use_keysArr = array();

            $row_num = -1;
            while (($rowArr = fgetcsv($handle, 0, ',')) !== false) {
                if ($row_num == -1) {
                    for ($i = 0; $i < count($rowArr); $i++) {
                        if (array_key_exists($rowArr[$i], $fieldsArr)) {
                            $use_keysArr[$i] = $fieldsArr[$rowArr[$i]];
                        }
                    }
                } else {
                    foreach ($use_keysArr as $idx => $field) {
                        if ($rowArr[$idx] == "FALSE") {
                            $rowArr[$idx] = 0;
                        } elseif ($rowArr[$idx] == "TRUE") {
                            $rowArr[$idx] = 1;
                        }
                        $csvArr[$row_num][$field] = $rowArr[$idx];
                    }
                }

                $row_num++;
            }

            return $csvArr;
        }

        return false;
    }

    public function companies()
    {
        $itemsArr = $this->get_csvArr('companies', $this->company_fieldsArr);
        $failedArr = array();

        foreach ($itemsArr as $companyArr) {
            $result = companiesManager::salesforce_add_company($companyArr);
            if (!$result) {
                $failedArr[] = $companyArr['Id'];
            }
        }

        return $failedArr;
    }

    public function company_rooms()
    {
        $itemsArr = $this->get_csvArr('company_rooms', $this->company_rooms_fieldsArr);
        $failedArr = array();

        $roomsArr = array();
        foreach ($itemsArr as $roomArr) {
            $account_id = $roomArr['AccountId'];
            unset($roomArr['AccountId']);

            $roomsArr[$account_id]['AccountId'] = $account_id;
            $roomsArr[$account_id]['Rooms'][] = $roomArr;
        }

        foreach ($roomsArr as $account_id => $groupArr) {
            $Company = companiesManager::get_by_salesforce_id($account_id);
            if (!$Company->id) {
                $failedArr[] = 'company: ' . $account_id;
            } else {
                foreach ($groupArr['Rooms'] as $roomArr) {
                    $result = roomsManager::salesforce_update_room($roomArr, $Company->id);
                    if (!$result) {
                        $failedArr[] = $roomArr['Id'];
                    }
                }
            }
        }

        return $failedArr;
    }

    public function users()
    {
        $itemsArr = $this->get_csvArr('users', $this->user_fieldsArr);
        $failedArr = array();

        foreach ($itemsArr as $userArr) {
            $result = User::salesforce_add_user($userArr);
            if (!$result) {
                $failedArr[] = $userArr['Id'];
            }
        }

        return $failedArr;
    }

    public function meeting_rooms()
    {
        $itemsArr = $this->get_csvArr('meeting_rooms', $this->meeting_room_fieldsArr);
        $failedArr = array();

        foreach ($itemsArr as $meeting_roomArr) {
            $result = meetingRoomsManager::salesforce_add_meeting_room($meeting_roomArr);
            if (!$result) {
                $failedArr[] = $meeting_roomArr['Id'];
            }
        }

        return $failedArr;
    }
}