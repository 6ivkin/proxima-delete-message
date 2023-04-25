<?
//JS extension register
CJSCore::RegisterExt('proxima.bootstrap4', [
    'js' => [
        '/local/modules/proxima.service/extension/bootstrap4/js/bootstrap.bundle.min.js',
    ],
    'css' => [
        '/local/modules/proxima.service/extension/bootstrap4/css/bootstrap.min.css',
    ],
    'rel'  => ['jquery2'],
    'skip_core' => true,
]);
CJSCore::RegisterExt('proxima.bootstrap5', [
    'js' => [
        '/local/modules/proxima.service/extension/bootstrap5/js/bootstrap.bundle.min.js',
    ],
    'css' => [
        '/local/modules/proxima.service/extension/bootstrap5/css/bootstrap.min.css',
    ],
    'rel'  => [],
    'skip_core' => true,
]);
CJSCore::RegisterExt('proxima.select2', [
    'js' => [
        '/local/modules/proxima.service/extension/select2/js/select2.min.js',
    ],
    'css' => [
        '/local/modules/proxima.service/extension/select2/css/select2.min.css',
    ],
    'rel'  => [],
    'skip_core' => true,
]);
CJSCore::RegisterExt('proxima.googlechart', [
    'js' => [
        'https://www.gstatic.com/charts/loader.js',
    ],
    'rel'  => [],
    'skip_core' => true,
]);
CJSCore::RegisterExt('proxima.sheetjs', [
    'js' => [
        '/local/modules/proxima.service/extension/sheetjs/js/xlsx.core.min.js',
    ],
    'rel'  => [],
    'skip_core' => true,
]);
CJSCore::RegisterExt('proxima.fixedColumns', [
    'js' => [
        '/local/modules/proxima.service/extension/fixedColumns/js/jquery.dataTables.min.js',
        '/local/modules/proxima.service/extension/fixedColumns/js/dataTables.fixedColumns.min.js',
    ],
    'css' => [
        '/local/modules/proxima.service/extension/fixedColumns/css/jquery.dataTables.min.css',
        '/local/modules/proxima.service/extension/fixedColumns/css/fixedColumns.dataTables.min.css',
    ],
    'rel'  => ['jquery2'],
    'skip_core' => true,
]);

//Custom autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    if(mb_substr($class, 0, 1) != '/')
        $class = '/' . $class;
    $path = __DIR__ . '/vendor' . $class . '.php';
    if(file_exists($path)) {
        require_once($path);
    }
});

//Backward compatibility
class_alias('\Proxima\Service\Component\RouterHelper', '\Proxima\Service\Component\Helper', true);