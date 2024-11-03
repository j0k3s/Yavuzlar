package main

import (
	"fmt"
	"os"
	"bufio"
	"strings"
)

type Admin struct {
	User
}

func (a *Admin) AddCustomer() {
	fmt.Println("Yeni müşteri ekleniyor...")
	fmt.Print("Kullanıcı adı: ")
	username, _ := reader.ReadString('\n')
	username = strings.TrimSpace(username)

	fmt.Print("Şifre: ")
	password, _ := reader.ReadString('\n')
	password = strings.TrimSpace(password)

	newUser := User{
		ID:       len(users) + 1,
		Username: username,
		Password: password,
		Type:     CustomerType,
	}
	users = append(users, newUser)
	fmt.Println("Yeni müşteri başarıyla eklendi.")
}

func (a *Admin) RemoveCustomer() {
	fmt.Print("Silinecek müşterinin kullanıcı adını girin: ")
	username, _ := reader.ReadString('\n')
	username = strings.TrimSpace(username)

	for i, user := range users {
		if user.Username == username && user.Type == CustomerType {
			users = append(users[:i], users[i+1:]...)
			fmt.Println("Müşteri başarıyla silindi.")
			return
		}
	}
	fmt.Println("Belirtilen kullanıcı adına sahip müşteri bulunamadı.")
}

func (a *Admin) ListLogs() {
	file, err := os.Open("log.txt")
	if err != nil {
		fmt.Println("Log dosyası açılamadı:", err)
		return
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)
	fmt.Println("Log Kayıtları:")
	for scanner.Scan() {
		fmt.Println(scanner.Text())
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("Log dosyası okunurken hata oluştu:", err)
	}
}