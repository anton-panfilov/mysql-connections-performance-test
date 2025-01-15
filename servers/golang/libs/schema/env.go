package schema

type Environment struct {
	// http server settings
	ServerPort uint `env:"HTTP_SERVER_PORT" envDefault:"8080" validate:"required,port"`

	// database connection credentials
	DbHost   string `env:"PERCONA_HOST,notEmpty" validate:"hostname"`
	DbPort   uint   `env:"PERCONA_PORT" envDefault:"3306" validate:"port"`
	DbUser   string `env:"PERCONA_USERNAME,notEmpty"`
	DbPass   string `env:"PERCONA_PASSWORD,notEmpty"`
	DbBase   string `env:"PERCONA_DATABASE,notEmpty"`
	DbParams string `env:"PERCONA_PARAMS" envDefault:"parseTime=true&interpolateParams=true" validate:"querystring"`
}

func (e Environment) Percona() Percona {
	return Percona{
		Host:       e.DbHost,
		Port:       e.DbPort,
		User:       e.DbUser,
		Pass:       e.DbPass,
		Base:       e.DbBase,
		Parameters: e.DbParams,
	}
}
