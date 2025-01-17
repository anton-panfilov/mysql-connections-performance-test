import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import * as clusterLib from 'cluster';
import * as process from 'process';
import * as OS from 'os';

const cluster:any = clusterLib

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  const port = process.env.PORT ?? 3000;
  await app.listen(port);
  console.log(`app listen ${port}`);
}


function clusterBoot(){
  const numCPUs = OS.availableParallelism();
  console.log(`CPU ${numCPUs}`);
  console.log(`CPU ${OS.cpus().length}`);

  if (cluster.isPrimary) {
    console.log(`Primary ${process.pid} is running`);


    // Fork workers.
    for (let i = 0; i < numCPUs ; i++) {
      cluster.fork();
    }

    cluster.on('exit', (worker, code, signal) => {
      console.log(`worker ${worker.process.pid} died`);
      cluster.fork();
    });
  } else {
    bootstrap()
  }
}

clusterBoot();
// bootstrap();
