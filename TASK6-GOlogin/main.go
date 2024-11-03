package main

import (
	"fmt"
	"bufio"
	"os"
	"strings"
)

var users []User
var reader = bufio.NewReader(os.Stdin)

func main() {
	initializeUsers()

	for {
		var userType int
		fmt.Print("Giriş tipi seçin (0: Admin, 1: Müşteri, 2: Çıkış): ")
		fmt.Scan(&userType)
		reader.ReadString('\n') 

		switch UserType(userType) {
		case AdminType:
			handleAdminLogin()
		case CustomerType:
			handleCustomerLogin()
		case 2:
			fmt.Println("Programdan çıkılıyor...")
			return
		default:
			fmt.Println("Geçersiz kullanıcı tipi!")
		}
	}
}

func initializeUsers() {
	users = append(users, User{ID: 1, Username: "admin", Password: "admin123", Type: AdminType})
	users = append(users, User{ID: 2, Username: "customer", Password: "cust123", Type: CustomerType})
}

func handleAdminLogin() {
	username, password := getUserCredentials()
	user, found := findUser(username, password, AdminType)
	if found {
		LogLogin(username, true)
		fmt.Println("Admin girişi başarılı!")
		adminMenu(user)
	} else {
		LogLogin(username, false)
		fmt.Println("Hatalı kullanıcı adı veya şifre!")
	}
}

func handleCustomerLogin() {
	username, password := getUserCredentials()
	user, found := findUser(username, password, CustomerType)
	if found {
		LogLogin(username, true)
		fmt.Println("Müşteri girişi başarılı!")
		customerMenu(user)
	} else {
		LogLogin(username, false)
		fmt.Println("Hatalı kullanıcı adı veya şifre!")
	}
}

func getUserCredentials() (string, string) {
	fmt.Print("Kullanıcı adı: ")
	username, _ := reader.ReadString('\n')
	username = strings.TrimSpace(username)

	fmt.Print("Şifre: ")
	password, _ := reader.ReadString('\n')
	password = strings.TrimSpace(password)

	return username, password
}

func findUser(username, password string, userType UserType) (User, bool) {
	for _, user := range users {
		if user.Username == username && user.Password == password && user.Type == userType {
			return user, true
		}
	}
	return User{}, false
}

func adminMenu(user User) {
	admin := Admin{User: user}
	for {
		fmt.Println("\nAdmin Menüsü:")
		fmt.Println("1. Müşteri Ekle")
		fmt.Println("2. Müşteri Sil")
		fmt.Println("3. Logları Listele")
		fmt.Println("4. Çıkış")
		fmt.Print("Seçiminiz: ")

		var choice int
		fmt.Scan(&choice)
		reader.ReadString('\n')

		switch choice {
		case 1:
			admin.AddCustomer()
		case 2:
			admin.RemoveCustomer()
		case 3:
			admin.ListLogs()
		case 4:
			return
		default:
			fmt.Println("Geçersiz seçim!")
		}
	}
}

func customerMenu(user User) {
	customer := Customer{User: user}
	for {
		fmt.Println("\nMüşteri Menüsü:")
		fmt.Println("1. Profili Görüntüle")
		fmt.Println("2. Şifre Değiştir")
		fmt.Println("3. Çıkış")
		fmt.Print("Seçiminiz: ")

		var choice int
		fmt.Scan(&choice)
		reader.ReadString('\n') 

		switch choice {
		case 1:
			customer.ViewProfile()
		case 2:
			customer.ChangePassword()
		case 3:
			return
		default:
			fmt.Println("Geçersiz seçim!")
		}
	}
}