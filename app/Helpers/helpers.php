<?php

if (!function_exists('formatPhoneNumber')) {

    function formatPhoneNumber($phone_number): array|bool|string|null
    {
        //  bila  penulisan  no  hp  0812  339  545
        $phone_number = str_replace('  ', '', $phone_number);
        //  bila  penulisan  no  hp  (0274)  778787
        $phone_number = str_replace('(', '', $phone_number);
        //  bila  penulisan  no  hp  (0274)  778787
        $phone_number = str_replace(')', '', $phone_number);
        //  bila  penulisan  no  hp  0811.239.345
        $phone_number = str_replace('.', '', $phone_number);
        //  bila  no  hp  terdapat  karakter  +  dan  0-9
        if (!preg_match('/[^+0-9]/', trim($phone_number))) {
            //  cek  karakter  1-3  apakah  +62
            if (substr(trim($phone_number), 0, 3) == '+62') {
                $phone_number = substr(trim($phone_number), 1);
            } //  cek  karakter  1  apakah  0
            elseif (substr(trim($phone_number), 0, 1) == '0') {
                $phone_number = '62' . substr(trim($phone_number), 1);
            }
        }

        return $phone_number;
    }
}