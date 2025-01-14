import { Controller, Get, Header, Query } from '@nestjs/common';
import { AppService } from './app.service';

@Controller()
export class AppController {
  constructor(private readonly appService: AppService) {}

  @Get('hello-world')
  @Header('Content-Type', 'text/plain')
  getHello(): string {
    return this.appService.getHello();
  }

  @Get('http-client')
  @Header('Content-Type', 'text/plain')
  getHttpClient(@Query('link') link: string): Promise<string> {
    return this.appService.getHttpClient(link);
  }
}
