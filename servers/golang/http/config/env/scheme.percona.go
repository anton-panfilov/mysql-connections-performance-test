package env

import "api/config"

func (e Environment) PerconaMain() config.Percona {
	return config.Percona{
		Host:       e.DbHost,
		Port:       e.DbPort,
		User:       e.DbUser,
		Pass:       e.DbPass,
		Base:       e.DbBase,
		Parameters: "parseTime=true&interpolateParams=true",
	}
}
