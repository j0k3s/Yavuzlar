package main

import (
	"fmt"
	"os"
	"time"
)

type LogType string

const (
	LoginSuccess    LogType = "Başarılı Giriş"
	LoginFailed     LogType = "Başarısız Giriş"
	PasswordChanged LogType = "Şifre Değişikliği"
	CustomerAdded   LogType = "Müşteri Eklendi"
	CustomerDeleted LogType = "Müşteri Silindi"
)

func writeLog(logType LogType, username string, details ...string) {
	timestamp := time.Now().Format("2006-01-02 15:04:05")
	var message string

	if len(details) > 0 {
		message = fmt.Sprintf("%s - %s: %s (%s)", timestamp, logType, username, details[0])
	} else {
		message = fmt.Sprintf("%s - %s: %s", timestamp, logType, username)
	}

	f, err := os.OpenFile("log.txt", os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		fmt.Println("Log dosyası açılamadı:", err)
		return
	}
	defer f.Close()

	if _, err := f.WriteString(message + "\n"); err != nil {
		fmt.Println("Log yazılamadı:", err)
	}
}

func listLogs() {
	data, err := os.ReadFile("log.txt")
	if err != nil {
		fmt.Println("Log dosyası okunamadı:", err)
		return
	}
	fmt.Println("\nLog Kayıtları:")
	fmt.Println(string(data))
}