package utils

import (
	"database/sql"
	"gorm.io/gorm"
)

var PerconaConnection *sql.DB
var PerconaConnectionGorm *gorm.DB

func InitPercona(PerconaConnection *sql.DB, PerconaConnectionGorm *gorm.DB) {

}
