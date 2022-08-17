## 1. 内核更新
1. 整数的 8 进制前缀支持,使用 0o/0O 前缀
   ```php
    <?php
    014;  // Non-prefix octal literal
    0o14; // Prefixed octal literal
    ?>
   ```

2. 支持数组解包
   ```php
   $arr1 = [1, 'a' => 'b'];
   $arr2 = [...$arr1, 'c' => 'd']; //[1, 'a' => 'b', 'c' => 'd']
   ```
   增加对函数参数解包后重新指定参数名：
   
   ```php
   function add(...$args, name:$params)
   ```
  
3. 新增 static 返回类型，可以返回子类实例。

   ```php
   class A {
      pubic static function getSelf() {
         return new self();
      }

      public static function getStatic():static {
         return new static();
      }
   }

   class B extend A {}

   B::class::getSelf() // 返回 A 的实例
   B::class::getStatic() // 返回 B 的实例
   ```

4. 支持函数参数命名，再也不用记参数的顺序了。

   ```php
   function test($name = null, $value == null) {
      echo $value;
   }

   test(value: 'test-value'); // 输出 test-value
   
   // 不过按照顺序传参还是兼容的
   test('test-name'); // 输出 test-name
   ```

4. 支持注解，注解的目标可以是类、方法、函数、参数、属性、类常量。

   ```php
   use MyExample\MyAttribute;

   #[Attribute]
   #[MyAttribute]
   #[\MyExample\MyAttribute]
   #[MyAttribute(1234)]
   #[MyAttribute(value: 1234)]
   #[MyAttribute(MyAttribute::VALUE)]
   #[MyAttribute(array("key" => "value"))]
   #[MyAttribute(100 + 200)]
   class Foo {}

   // 组合使用
   #[MyAttribute(1234), MyAttribute(5678)]
   class AnotherFoo
   {
   } 
   ```

5. 支持构造器属性提升，也就是说可以在构造函数中定义类的属性:

   ```php
   class Point
   {
      public function __construct(public int $x, protected int $y){}
   }

   $p = new Point(10, 20);
   echo $p->x;
   ```

6. 新增联合类型，可联合类型接受多个不同的简单类型做为参数。例如 T1|T2|null 代表接受一个空值为参数。

7. 新增 match 表达式：

   ```php
   $return_value = match (subject_expression) {
      single_conditional_expression => return_expression,
      conditional_expression1, conditional_expression2 => return_expression,
   };

   $food = 'cake';
   $return_value = match ($food) {
      'apple' => 'This food is an apple',
      'bar' => 'This food is a bar',
      'cake' => 'This food is a cake',
   };

   var_dump($return_value);
   ```

   match 与 switch 的区别：
   1. 它会像三元表达式一样求值;
   2. 它的比较是严格比较(===)，而 switch 是松散比较(==)

8. 新增 Nullsafe 运算符(玩过 rust 和 typescript 同学对此应该不陌生)。

   ```php
   // 自 PHP 8.0.0 起可用
   $result = $repository?->getUser(5)?->name;

   // 上边那行代码等价于以下代码
   if (is_null($repository)) {
      $result = null;
   } else {
      $user = $repository->getUser(5);
      if (is_null($user)) {
         $result = null;
      } else {
         $result = $user->name;
      }
   }
   ```

9. 新增 WeakMap 类，WeakMap 中的对象 key 不影响对象的引用计数。也就是说，如果在任何时候对其唯一的剩余引用是 WeakMap key，那么该对象将会被垃圾收集并从 WeakMap 移除。


10. 只要类型兼容，任意数量的函数参数都可以用一个可变参数替换:

   ```php
   class A {
     public function method(int $many, string $parameters, $here) {}
   }
   class B extends A {
      public function method(...$everything) {}
   }
   ```
11. 可以通过 `$object::class` 获取类名，返回的结果和 `get_class($object)` 一致。
12. new、instanceof 可用于任何表达式， 用法为 `new (expression)(...$args)` 和 `$obj instanceof (expression)`。
13. 添加对一些变量语法一致性的修复，例如现在能够编写 `Foo::BAR::$baz`。
14. 添加 Stringable 接口， 当一个类定义 `__toString()` 方法后会自动实现该接口。

   ```php
   class Foo
   {
      public function __toString(): string
      {
         return 'foo';
      }
   }

   function bar(Stringable $stringable) { /* … */ }

   bar(new Foo());
   bar('abc');
   ```

15. Trait 可以定义私有抽象方法（abstract private method）。 类必须实现 trait 定义的该方法。
16. 参数列表中的末尾逗号为可选。

   ```php
   function functionWithLongSignature(
      Type1 $parameter1,
      Type2 $parameter2, // <-- 这个逗号也被允许了
   ) {}
   ```
17. 现在允许 `catch (Exception)` 一个 exception 而无需捕获到变量中。

   ```php
   try {
      // some code
   } catch(Exception) { // 现在后面这个 $e 变量不需要用的话，就可以省略不写。
      //
   }
   ```
18. 新增了 str_contains(), str_starts_with() and str_ends_with() 等字符串处理函数。
19. 新增 mixed 类型，可以作为函数的参数和返回值类型。要警惕 mixed 类型的滥用，能用基本类型和联合类型解决就不要使用 mixed。
20. 支持 throw 从一个语句更改为一个表达式，这使得可以在很多新地方抛出异常：
   ```php
   $triggerError = fn () => throw new MyError();
   $foo = $bar['offset'] ?? throw new OffsetDoesNotExist('offset');
   ```

剩下的就是一些 PHP 扩展更新和优化，比如 PDO，FPM，OpenSSL等，对使用影响不大。