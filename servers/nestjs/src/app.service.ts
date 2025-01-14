import { BadRequestException, Injectable } from '@nestjs/common';

@Injectable()
export class AppService {
  getHello(): string {
    return 'Hello World!';
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
      return JSON.stringify(await response.json());
    } catch (err) {
      throw new BadRequestException(err);
    }
  }
}
