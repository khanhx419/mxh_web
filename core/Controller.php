<?php

/**
 * Base Controller
 * Cung cấp các phương thức dùng chung cho tất cả controllers
 */
class Controller
{

    /**
     * Render view với layout
     */
    protected function view($view, $data = [], $layout = 'app')
    {
        // Extract data thành biến
        extract($data);

        // Bắt đầu output buffering cho nội dung view
        ob_start();
        $viewFile = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$view}");
        }

        require $viewFile;
        $content = ob_get_clean();

        // Render layout
        $layoutFile = BASE_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Render view không có layout
     */
    protected function viewOnly($view, $data = [])
    {
        extract($data);
        $viewFile = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$view}");
        }

        require $viewFile;
    }

    /**
     * Trả về JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lấy Model instance
     */
    protected function model($model)
    {
        $modelFile = BASE_PATH . '/app/Models/' . $model . '.php';

        if (!file_exists($modelFile)) {
            throw new Exception("Model not found: {$model}");
        }

        require_once $modelFile;
        return new $model();
    }

    /**
     * Validate input
     */
    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && (empty($value) && $value !== '0')) {
                    $errors[$field] = "Trường {$field} là bắt buộc.";
                }
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "Trường {$field} phải có ít nhất {$min} ký tự.";
                    }
                }
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = "Trường {$field} không được quá {$max} ký tự.";
                    }
                }
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Email không hợp lệ.";
                }
                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = "Trường {$field} phải là số.";
                }
            }
        }

        return $errors;
    }
}
