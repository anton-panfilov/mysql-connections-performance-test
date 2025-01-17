import { BadRequestException, Injectable } from '@nestjs/common';

@Injectable()
export class AppService {
  getHello(): string {
    return 'Hello World';
  }

  sleep(sec: number): Promise<string> {
    return new Promise((resolve)=>{
      setTimeout(()=>{
        resolve('Hello Sleep')
      }, sec*1000)
    })
  }

  sleep_no_promise(sec: number): string {
    for(let i = 0; i < sec*1000; i++){
      Math.random()
    }
    return "sleep no promise"
  }

  async getHttpClient(link: string): Promise<string> {
    if (!link) {
      throw new BadRequestException("Query parameter 'link' is required");
    }
    let url = null;

    try {
      url = new URL(link);
    } catch (err) {
      throw new BadRequestException("Invalid 'link' URL");
    }

    try {
      const response = await fetch(url);
      return await response.text();
    } catch (err) {
      throw new BadRequestException(err);
    }
  }
}
