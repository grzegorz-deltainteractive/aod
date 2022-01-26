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
            'edit' => 'Edytuj',
            'categories' => 'Kategorie ankiety',
            'displaypools' => 'Średnia ankiet'
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
function median($values) {
    $values = array_values($values);
    $count = count($values);
    if ($count === 0)  return 0;
    asort($values);
    $half = floor($count / 2);
    if ($count % 2) return $values[$half];
    return ($values[$half - 1] + $values[$half]) / 2.0;
}
