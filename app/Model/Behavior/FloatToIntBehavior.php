<?php
class FloatToIntBehavior extends ModelBehavior {
    public function afterFind(Model $model, $results, $primary = false) {
        foreach ($results as $x=>$row) {
            foreach ($model->schema() as $field => $defs) {
                if (!isset($row[$model->alias][$field])) continue;
                switch ($defs['type']) {
                    case 'float':
                        $results[$x][$model->alias][$field] = (int)$row[$model->alias][$field];
                        break;

                    default:
                        break;
                }
            }
        }
        return $results;
    }
}