<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 29.01.18
 * Time: 7:46
 */

namespace AppBundle\Formatter;


class CardFormatter
{

    public function cardList($rowResult = [])
    {
        $result = [];

        foreach ($rowResult as $row) {
            $card = [
                'id' => $row['id'],
                'carOwner' => [
                    'type' => $row['property_type'] == 1 ? 'Физ. лицо' : 'Юр. лицо',
                    'name' => [
                        'last' => $row['owner_last_name'],
                        'first' => $row['owner_first_name'],
                        'middle' => $row['owner_middle_name']
                    ]
                ],
                'regDocProperties' => [
                    'plate' => !empty($row['registration_number']) ? $row['registration_number'] : 'В744УО178'
                ],
                'carProperties' => [
                    'vin' => $row['vin'],
                    'mark' => $row['car_mark'],
                    'model' => $row['car_model'],
                    'category' => $row['category_id']
                ],
                'eaistoNumber' => $row['eaisto_number'],
                'eaistoDate' => $row['eaisto_date'] * 1000,
                'status' => $row['status'] == 1 ? 'created' : 'issued',
                'validTill' => $row['valid_till'] * 1000,
                'createdAt' => $row['created_at'] * 1000,
                'createdBy' => $row['user_id']
            ];

            $result[] = $card;
        }

        return $result;
    }

    public function applicationList($rowResult = [])
    {
        $result = [];

        foreach ($rowResult as $row) {
            $result[] = [
                'id' => (int) $row['id'],
                'createdAt' => $row['createdAt'] * 1000,
                'registrationNumber' => $row['registrationNumber'],
                'vin' => $row['vin'],
                'email' => $row['email'],
                'eaistoNumber' => $row['eaistoNumber'],
                'eaistoStatus' => $row['eaistoStatus'],
                'purchased' => (boolean) $row['purchased'],
                'type' => (int) $row['type'],
                'photo1' => $row['photo1'],
                'photo2' => $row['photo2']
            ];
        }

        return $result;
    }

}