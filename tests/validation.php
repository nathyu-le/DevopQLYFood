<?php
declare(strict_types=1);
require __DIR__ . '/../app/bootstrap.php';
$passed = 0;
function verify(bool $condition, string $message): void
{
    global $passed;
    if (!$condition) {
        fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
        exit(1);
    }
    ++$passed;
    echo 'PASS: ' . $message . PHP_EOL;
}
$valid = ['name' => 'Cơm gà', 'category' => 'main', 'price' => '45000', 'description' => 'Món thử nghiệm', 'is_available' => '1'];
verify(validate_dish($valid)[1] === [], 'accept a valid Vietnamese dish');
foreach (['-1', '1.5', '1e4', '45000abc', '1000000000'] as $badPrice) {
    verify(isset(validate_dish([...$valid, 'price' => $badPrice])[1]['price']), 'reject invalid price: ' . $badPrice);
}
verify(validate_dish([...$valid, 'price' => '0'])[1] === [], 'accept zero price');
verify(isset(validate_dish([...$valid, 'name' => '   '])[1]['name']), 'reject empty dish name');
verify(isset(validate_dish([...$valid, 'name' => str_repeat('ế', 161)])[1]['name']), 'enforce Unicode name limit');
verify(isset(validate_dish([...$valid, 'category' => 'unknown'])[1]['category']), 'reject unknown category');
verify(isset(validate_dish([...$valid, 'is_available' => '2'])[1]['is_available']), 'reject unknown availability');
verify(isset(validate_dish([...$valid, 'name' => ['invalid']])[1]['name']), 'reject array input without type error');
verify(e('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;', 'escape HTML output');
verify(positive_id('-1') === null && positive_id('abc') === null && positive_id('42') === 42, 'validate record identifiers');
echo $passed . " validation checks passed.\n";
