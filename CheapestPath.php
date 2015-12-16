<?php

/**
 * Класс поиска оптимального (дешевого) марщрута
 *
 * Используется алгоритм Дейкстры, который находит
 * кратчайшие пути от одной из вершин графа до всех остальных.
 *
 * @link https://ru.wikipedia.org/wiki/Алгоритм_Дейкстры
 *
 * @see example.php
 *
 */
class CheapestPath
{
    /**
     * Граф маршрутов
     *
     * @var array
     */
    protected $graph = [];

    /**
     * Сеттер графа
     *
     * @param array $graph Граф
     *
     * @return void
     */
    public function setGraph($graph)
    {
        $this->graph = $graph;
    }

    /**
     * Представим граф в виде списка
     *
     * @param array $data Список остановок вида:
     * [[A, B, 1], [B, C, 1], [C, D, 3]]
     * Где:
     *   А, B, C, D - остановки
     *   1, 3 - стоимость проезда между остановками
     *
     * @throws \Exception
     *
     * @return void
     */
    public function prepareGraph($data)
    {
        $this->graph = [];

        foreach ($data as $row) {
            if (!isset($row[0], $row[1], $row[2])) {
                throw new Exception('Данные не валидны');
            }

            $this->graph[$row[0]][$row[1]] = $row[2];
            $this->graph[$row[1]][$row[0]] = $row[2];
        }
    }

    /**
     * Получить самый дешевый маршрут
     *
     * @param string $from Начало маршрута
     * @param string $to Конец маршрута
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getPath($from, $to)
    {
        /**
         * Массив с результатом
         */
        $result = [];

        /**
         * Массив кратчайших путей к каждому узлу
         */
        $d = [];

        /**
         * Массив "предшественников" для каждого узла
         */
        $pi = [];

        /**
         * Очередь всех неоптимизированных узлов
         */
        $queue = new SplPriorityQueue();

        foreach ($this->graph as $v => $adj) {
            /**
             * Устанавливаем изначальные расстояния как бесконечность
             */
            $d[$v] = INF;

            /**
             * Никаких узлов позади нет
             */
            $pi[$v] = null;

            foreach ($adj as $w => $cost) {
                /**
                 * Воспользуемся ценой связи как приоритетом
                 */
                $queue->insert($w, $cost);
            }
        }

        /**
         * Начальная дистанция на стартовом узле - 0
         */
        $d[$from] = 0;

        while (!$queue->isEmpty()) {
            /**
             * Извлечем минимальную цену
             */
            $u = $queue->extract();

            if (!empty($this->graph[$u])) {
                /**
                 * Пройдемся по всем соседним узлам
                 */
                foreach ($this->graph[$u] as $v => $cost) {
                    /**
                     * Установим новую длину пути для соседнего узла
                     */
                    $alt = $d[$u] + $cost;

                    /**
                     * Если он оказался короче
                     *   update minimum length to vertex установим как минимальное расстояние до этого узла
                     *   добавим соседа как предшествующий этому узла
                     */
                    if ($alt < $d[$v]) {
                        $d[$v] = $alt;
                        $pi[$v] = $u;
                    }
                }
            }
        }

        /**
         * Теперь мы можем найти минимальный путь используя обратный проход
         */
        $stack = new SplStack();
        $u = $to;
        $result['sum'] = 0;

        /**
         * Проход от целевого узла до стартового
         */
        while (isset($pi[$u]) && $pi[$u]) {
            $stack->push($u);

            /**
             * Добавим стоимость для предшествующих
             */
            $result['sum'] += $this->graph[$u][$pi[$u]];
            $u = $pi[$u];
        }

        /**
         * Стек будет пустой, если нет пути назад
         */
        if ($stack->isEmpty()) {
            throw new Exception('Нет пути из ' . $from . ' в ' . $to);
        } else {
            /**
             * Добавим стартовый узел и покажем весь путь в обратном (LIFO) порядке
             */
            $stack->push($from);

            foreach ($stack as $v) {
                $result['path'][] = $v;
            }
        }

        return $result;
    }
}