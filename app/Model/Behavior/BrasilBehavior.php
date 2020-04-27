<?php
class BrasilBehavior extends ModelBehavior {
    public function beforeSave(Model $model, $options = array()){
        if (!empty($model->data[$model->alias]['id'])){
            unset($model->data[$model->alias]['created']);
            unset($model->data[$model->alias]['modified']);
        }
        $schema = $model->schema();
        foreach ($schema as $field => $defs) {
            if (!isset($model->data[$model->alias][$field])) continue;
            if ($field=='created' || $field=='modified') continue;
            //if ($field=='valor_carga') die($defs['type']);
            switch ($defs['type']) {
                case 'date':
                    $model->data[$model->alias][$field] = DataToSQL($model->data[$model->alias][$field]);
                    break;

                case 'datetime':
                    $model->data[$model->alias][$field] = DataHoraToSQL($model->data[$model->alias][$field]);
                    break;

                case 'float':
                case 'decimal':
                    $model->data[$model->alias][$field] = FloatToSQL($model->data[$model->alias][$field], $defs['length']);
                    break;

                default:
                    break;
            }
        }
        return true;
    }
    
    public function afterFind(Model $model, $results, $primary = false) {
        foreach ($results as $x=>$row) {
            foreach ($model->schema() as $field => $defs) {
                if (!isset($row[$model->alias][$field])) continue;
                switch ($defs['type']) {
                    case 'date':
                        $results[$x][$model->alias][$field] = DataFromSQL($results[$x][$model->alias][$field]);
                        break;

                    case 'datetime':
                        $results[$x][$model->alias][$field] = DataHoraFromSQL($results[$x][$model->alias][$field]);
                        break;

                    case 'float':
                    case 'decimal':
                        $results[$x][$model->alias][$field] = FloatFromSQL($results[$x][$model->alias][$field], $defs);
                        break;

                    default:
                        break;
                }
            }
        }
        return $results;
    }
}