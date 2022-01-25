<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 25/01/2022
 * Time: 23:18
 */

if (!function_exists('getBreadcrumbs')) {
    function getBreadcrumbs($segments)
    {
        $translations = [
            'suppliers' => 'Dostawcy',
            'pools' => 'Ankiety',
            'laboratories' => 'Laboratoria',
            'departments' => 'Działy',
            'settings' => 'Ustawienia',
            'users' => 'Użytkownicy',
            'roles' => 'Uprawnienia',
            'edit' => 'Edytuj'
        ];
        foreach ($segments as &$segment) {
            if (array_key_exists($segment, $translations)) {
                $segment = [
                    'title' => $translations[$segment],
                    'url' => $segment
                ];
            } else {
                $segment = [
                    'title' => $segment,
                    'url' => ''
                ];
            }
        }
        unset ($segment);
        return $segments;
    }
}
