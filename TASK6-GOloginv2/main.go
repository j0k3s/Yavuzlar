package main

import (
	"bufio"
	"fmt"
	"os"
	"strings"
)

var (
	users  []User
	reader = bufio.NewReader(os.Stdin)
)

func main() {
	LoadUsers()
	for {
		fmt.Println("\n0. Admin Girişi")
		fmt.Println("1. Müşteri Girişi")
		fmt.Println("2. Çıkış")
		fmt.Print("Seçiminiz: ")

		choice, _ := reader.ReadString('\n')
		choice = strings.TrimSpace(choice)

		switch choice {
		case "0":
			loginAs(AdminType)
		case "1":
			loginAs(CustomerType)
		case "2":
			SaveUsers()
			fmt.Println("Programdan çıkılıyor...")
			return
		default:
			fmt.Println("Geçersiz seçim!")
		}
	}
}

func loginAs(userType string) {
	fmt.Print("Kullanıcı adı: ")
	username, _ := reader.ReadString('\n')
	username = strings.TrimSpace(username)

	fmt.Print("Şifre: ")
	password, _ := reader.ReadString('\n')
	password = strings.TrimSpace(password)

	for i := range users {
		if users[i].Username == username && users[i].Password == password && users[i].Type == userType {
			writeLog(LoginSuccess, username, userType)
			if users[i].Type == AdminType {
				adminMenu(&users[i])
			} else {
				customerMenu(&Customer{User: &users[i]})
			}
			return
		}
	}
	writeLog(LoginFailed, username, userType)
	fmt.Println("Hatalı kullanıcı adı veya şifre!")
}

func adminMenu(admin *User) {
	for {
		fmt.Println("\nAdmin Menü")
		fmt.Println("1. Müşteri Ekle")
		fmt.Println("2. Müşteri Sil")
		fmt.Println("3. Log Listele")
		fmt.Println("4. Kullanıcıları Listele")
		fmt.Println("5. Çıkış")
		fmt.Print("Seçiminiz: ")

		choice, _ := reader.ReadString('\n')
		choice = strings.TrimSpace(choice)

		switch choice {
		case "1":
			addCustomer()
		case "2":
			deleteCustomer()
		case "3":
			listLogs()
		case "4":
			listUsers()
		case "5":
			return
		default:
			fmt.Println("Geçersiz seçim!")
		}
	}
}

func customerMenu(customer *Customer) {
	for {
		fmt.Println("\nMüşteri Menü")
		fmt.Println("1. Profili Görüntüle")
		fmt.Println("2. Şifre Değiştir")
		fmt.Println("3. Çıkış")
		fmt.Print("Seçiminiz: ")

		choice, _ := reader.ReadString('\n')
		choice = strings.TrimSpace(choice)

		switch choice {
		case "1":
			customer.ViewProfile()
		case "2":
			customer.ChangePassword()
		case "3":
			return
		default:
			fmt.Println("Geçersiz seçim!")
		}
	}
}

func addCustomer() {
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
	SaveUsers()
	writeLog(CustomerAdded, username)
	fmt.Println("Müşteri başarıyla eklendi!")
}

func deleteCustomer() {
	fmt.Print("Silinecek müşterinin kullanıcı adı: ")
	username, _ := reader.ReadString('\n')
	username = strings.TrimSpace(username)

	for i := range users {
		if users[i].Username == username && users[i].Type == CustomerType {
			users = append(users[:i], users[i+1:]...)
			SaveUsers()
			writeLog(CustomerDeleted, username)
			fmt.Println("Müşteri başarıyla silindi!")
			return
		}
	}
	fmt.Println("Müşteri bulunamadı!")
}

func listUsers() {
	fmt.Println("\nKullanıcı Listesi:")
	for _, user := range users {
		fmt.Printf("ID: %d, Kullanıcı Adı: %s, Tip: %s\n", user.ID, user.Username, user.Type)
	}
}