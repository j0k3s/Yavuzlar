package main

import (
    "fmt"
    "os"
    "time"
)

func LogLogin(username string, success bool) error {
    file, err := os.OpenFile("log.txt", os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
    if err != nil {
        return err
    }
    defer file.Close()

    status := "Başarısız"
    if success {
        status = "Başarılı"
    }

    logEntry := fmt.Sprintf("%s - Kullanıcı: %s, Giriş Durumu: %s\n", time.Now().Format("2006-01-02 15:04:05"), username, status)
    
    _, err = file.WriteString(logEntry)
    return err
}