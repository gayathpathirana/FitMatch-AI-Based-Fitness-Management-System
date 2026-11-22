<?php

class Member 
{
    private string $name;
    private string $email;
    private string $passwordHash;
    private string $role;

    public function __construct(string $name, string $email, string $password, string $role = "client")
    {
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRole($role);
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format.");
        }
        $this->email = $email;
    }

    public function setPassword(string $plainPassword): void
    {
        if (strlen($plainPassword) < 6) {
            throw new InvalidArgumentException("Password must be at least 6 characters.");
        }
        $this->passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function setRole(string $role): void
    {
        $allowed = ["client", "coach", "admin"];
        $this->role = in_array($role, $allowed) ? $role : "client";
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }
}

echo "<h2>Encapsulation Demo (FitMatch style)</h2>";

try {
    $member = new Member("Nimal Perera", "nimal@example.com", "secret123", "coach");

    echo "Name: " . htmlspecialchars($member->getName()) . "<br>";
    echo "Email: " . htmlspecialchars($member->getEmail()) . "<br>";
    echo "Role: " . htmlspecialchars($member->getRole()) . "<br>";

    var_dump($member->verifyPassword("secret123"));
    var_dump($member->verifyPassword("wrongpass"));

    $member->setEmail("not-an-email");

} catch (InvalidArgumentException $e) {
    echo "<p style='color:red'>" . htmlspecialchars($e->getMessage()) . "</p>";
}