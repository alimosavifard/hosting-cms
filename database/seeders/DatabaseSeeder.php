<?php
class Seeder_DatabaseSeeder
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function run()
    {
        // افزودن محصولات نمونه
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM products");
        if ($stmt->fetchColumn() == 0) {
            $sample_products = [
                ['name' => 'هاست اشتراکی', 'price' => 50000, 'description' => 'هاست اشتراکی با 1 گیگابایت فضا', 'type' => 'hosting'],
                ['name' => 'دامنه .com', 'price' => 200000, 'description' => 'دامنه دات کام برای یک سال', 'type' => 'domain'],
                ['name' => 'VPS پایه', 'price' => 300000, 'description' => 'VPS با 2 گیگابایت رم و 50 گیگابایت SSD', 'type' => 'vps']
            ];

            $stmt = $this->pdo->prepare("INSERT INTO products (name, price, description, type) VALUES (?, ?, ?, ?)");
            foreach ($sample_products as $product) {
                $stmt->execute([$product['name'], $product['price'], $product['description'], $product['type']]);
            }
        }

        // افزودن کاربر ادمین
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $admin = [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'role' => 'admin'
            ];
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$admin['username'], $admin['email'], $admin['password'], $admin['role']]);
        }
    }
}
?>