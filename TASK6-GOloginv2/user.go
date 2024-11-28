package main

import (
	"encoding/json"
	"fmt"
	"os"
	"strings"
)

const (
	AdminType    = "admin"
	CustomerType = "customer"
)

type User struct {
	ID       int    `json:"id"`
	Username string `json:"username"`
	Password string `json:"password"`
	Type     string `json:"type"`
}

type Customer struct {
	*User
}

func SaveUsers() error {
	data, err := json.MarshalIndent(users, "", "    ")
	if err != nil {
		return fmt.Errorf("kullanıcılar JSON'a dönüştürülemedi: %w", err)
	}

	if err := os.WriteFile("users.json", data, 0644); err != nil {
		return fmt.Errorf("dosya yazılamadı: %w", err)
	}

	return nil
}

func LoadUsers() error {
	data, err := os.ReadFile("users.json")
	if err != nil {
		users = []User{
			{ID: 1, Username: "admin", Password: "admin123", Type: AdminType},
			{ID: 2, Username: "user1", Password: "pass123", Type: CustomerType},
		}
		return SaveUsers()
	}

	if err := json.Unmarshal(data, &users); err != nil {
		return fmt.Errorf("kullanıcılar yüklenemedi: %w", err)
	}

	return nil
}

func (c *Customer) ViewProfile() {
	fmt.Printf("\nKullanıcı Bilgileri:\n")
	fmt.Printf("ID: %d\n", c.ID)
	fmt.Printf("Kullanıcı Adı: %s\n", c.Username)
	fmt.Printf("Kullanıcı Tipi: %s\n", c.Type)
}

func (c *Customer) ChangePassword() {
	fmt.Print("Yeni şifre: ")
	newPassword, err := reader.ReadString('\n')
	if err != nil {
		fmt.Println("Şifre okunamadı:", err)
		return
	}

	newPassword = strings.TrimSpace(newPassword)
	if len(newPassword) < 6 {
		fmt.Println("Şifre en az 6 karakter olmalıdır")
		return
	}

	for i := range users {
		if users[i].ID == c.ID {
			users[i].Password = newPassword
			c.Password = newPassword
			break
		}
	}

	if err := SaveUsers(); err != nil {
		fmt.Println("Şifre kaydedilemedi:", err)
		return
	}

	writeLog(PasswordChanged, c.Username)
	fmt.Println("Şifre başarıyla değiştirildi.")
}