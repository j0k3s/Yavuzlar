package main

type UserType int

const (
    AdminType UserType = iota
    CustomerType
)

type User struct {
    ID       int
    Username string
    Password string
    Type     UserType
}