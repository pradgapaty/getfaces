<?php
switch ($expr) {
case 0:
    echo 'First case, with a break';
    break;
case 1:
    echo 'Second case, which falls through';
    // no break
case 2:
case   3:
Case 4:
    echo 'Third case, return instead of break';
    return;
Default:
    echo 'Default case';
    break;
}

switch ($expr) {
case 0:
    echo 'First case,';

case 1 :
    echo 'Second case';
    // no break
case 2:
case 3:
    echo 'Third case';
    return;

default:
    echo 'Default case';
    break;
}

switch ($foo) {
case'Foo': {
    echo 'foo';
    break;
}
}

while ($i < 10) {
    switch ($foo) {
    case '1':
    case '2':
        ++$i;
        continue 2;
    case '3':
        return $i;
    }
}

switch (true) {
case is_resource($value):
    throw new Exception('foo');
case is_object($value):
    return 'object';
}

switch (0) {
case 0:
    switch (1) {
    case 1:
        echo 'a';
        break;
    }
    break;
}

switch ($foo) {
case Foo::ONE:
case Foo::TWO:
case Foo::Class:
    break;
}

switch (true) {
case $value instanceof StdClass:
    return 1;
case strpos('_', get_class($value)) !== false:
    break;
}

switch (true) {
case $value instanceof StdClass:
    if ($value) {
        return null;
    }
}

use Vendor\Test\FooBar;

function test()
{
    switch ($val) {
    case 'foo':
        echo 'foo';
        break;
    default:
        echo 'foo';
    }

    exit;
}

switch ($foo) {
case 1: $bar = 1; 
    break;
case 2:
    
    $bar = 2; 
    break;
case 21:
case 3: 
    return 3;
default: $bar = 0;
}

switch ($foo) {
case 'foo': // some comment
    echo 'foo';
    break;
case 'bar':
    // some comment
    echo 'bar';
    break;
case 'baz': // phpcs:ignore Standard.Category.Sniff
    echo 'baz';
    break;
case 'boo':

    // other comment
    echo 'boo';
    break;
default: // other comment
    echo 'default';
    break;
}

switch($foo)
{
case ('foo'):
default:
{
    $foo = 'foo';
    break;
}
}

switch ($foo) {
case Foo::INTERFACE:
    return self::INTERFACE;
case Foo::NAMESPACE:
    return self::MODULE;
case Foo::TRAIT:
case Foo::ARRAY:
    return self::VALUE;
}

// OK: Every clause terminates
switch ($foo) {
case 1:
    if ($bar > 0) {
        return 0;
    } else {
        return 1;
    }
case 2:
    return 2;
}

// ERROR: No else clause
switch ($foo) {
case 1:
    if ($bar > 0) {
        return 0;
    } elseif ($bar < 0) {
        return 1;
    }
case 2:
    return 2;
}

// OK: No fall-through present
switch ($foo) {
case 1:
    if ($bar > 0) {
        return 0;
    } elseif ($bar < 0) {
        return 1;
    }
}

// ERROR: No else clause (nested)
switch ($foo) {
case 1:
    if ($bar > 0) {
        return 0;
    } else {
        if ($foo > $bar) {
            continue;
        }
    }
case 2:
    return 2;
}

// OK: Every clause terminates
switch ($foo) {
case 1:
    if ($bar > 0) {
        return 0;
    } else {
        if ($foo > $bar) {
            continue;
        } else {
            break;
        }
    }
case 2:
    return 2;
}

// ERROR: Non-termination IF clause
switch ($foo) {
case 1:
    if ($bar > 0) {
        $offset = 0;
    } else {
        break;
    }
case 2:
    return 2;
}

// ERROR: Non-termination IF clause (nested)
switch ($foo) {
case 1:
    if ($bar > 0) {
        continue;
    } else {
        if ($foo > $bar) {
            $offset = 0;
        } else {
            break;
        }
    }
case 2:
    return 2;
}

switch ($sContext)
{
case 'SOMETHING':
case 'CONSTANT':
    do_something();
    break;
case 'GLOBAL':
case 'GLOBAL1':
    do_something();
    // Fall through
default:
{
    do_something();
}
}

$foo = $foo ?
    function () {
        switch ($a) {
        case 'a':
            break;
        }
    } :
    null;

switch ($foo) {
case Foo::INTERFACE:
    echo '1';
    return self::INTERFACE;
case Foo::TRAIT:
case Foo::ARRAY:
    echo '1';
    return self::VALUE;
}

// OK: Every clause terminates
switch ($foo) {
case 1:
    switch ($bar) {
    case 1:
        return 1;
    default:
        return 3;
    }
    case 2:
    return 2;
}

// KO: Not every clause terminates
switch ($foo) {
case 1:
    switch ($bar) {
    case 1:
        return;
    }
    case 2:
    return 2;
}

// KO: Not every clause terminates
switch ($foo) {
case 1:
    switch ($bar) {
    case 1:
        return;
    default:
        $a = 1;
    }
case 2:
    return 2;
}

// OK: Every clause terminates
switch ($foo) {
case 1:
    switch ($bar) {
    case 1:
        return 1;
    default:
        throw new \Exception();
    }
    case 2:
    return 2;
}
