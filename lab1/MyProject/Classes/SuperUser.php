<?php
declare(strict_types=1);
namespace MyProject\Classes;


class SuperUser extends User implements SuperUserInterface
{
    public $role;

    public static $superUserCount = 0;

    /**
     * Конструктор класса SuperUser.
     */
    public function __construct($name, $login, $password, $role)
    {
        parent::__construct($name, $login, $password);
        $this->role = $role;
    }

    /**
     * Переопределяем регистрацию: супер-юзер увеличивает
     * только свой счётчик, а не счётчик обычных пользователей.
     */
    protected function register(): void
    {
        self::$superUserCount++;
    }

    /**
     * Возвращает HTML с информацией о суперпользователе.
     */
    public function showInfo(): string
    {
        return "<div class=\"super-user-info\">
                    <h3>Super User Info</h3>
                    <p><strong>Name:</strong> {$this->name}</p>
                    <p><strong>Login:</strong> {$this->login}</p>
                    <p><strong>Role:</strong> {$this->role}</p>
                </div>";
    }

    /**
     * Получить информацию о суперпользователе в виде ассоциативного массива
     */
    public function getInfo(): array
    {
        return [
            'name'  => $this->name,
            'login' => $this->login,
            'role'  => $this->role,
        ];
    }
}
