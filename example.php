<?php
require_once 'CheapestPath.php';

/**
 * Список остановок вида:
 * [[A, B, 1], [B, C, 1], [C, D, 3]]
 * Где:
 *   А, B, C, D - остановки
 *   1, 3 - стоимость проезда между остановками
 */
$map = [
    ['A', 'B', 3],
    ['A', 'D', 3],
    ['A', 'F', 6],
    ['B', 'D', 1],
    ['B', 'E', 3],
    ['C', 'E', 2],
    ['C', 'F', 3],
    ['D', 'E', 1],
    ['D', 'F', 2],
    ['E', 'F', 5],
];

$path = new CheapestPath();
$path->prepareGraph($map);
$result = $path->getPath('F', 'A');

echo "Сумма проезда: " . $result['sum'] . "\n";
echo "Путь: " . implode(' -> ', $result['path']) . "\n";
