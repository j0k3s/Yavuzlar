package main

import (
	"fmt"
	"strings"
)

type Customer struct {
	User
}

func (c *Customer) ViewProfile() {
	fmt.Println("Profil Bilgileri:")
	fmt.Printf("Kullanıcı Adı: %s\n", c.Username)
	fmt.Printf("Kullanıcı ID: %d\n", c.ID)
}

func (c *Customer) ChangePassword() {
	fmt.Print("Yeni şifre: ")
	newPassword, _ := reader.ReadString('\n')
	newPassword = strings.TrimSpace(newPassword)

	c.Password = newPassword
	fmt.Println("Şifre başarıyla değiştirildi.")
}