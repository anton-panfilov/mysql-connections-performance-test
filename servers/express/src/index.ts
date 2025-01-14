import express, { Express, Request, Response } from 'express';
import dotenv from 'dotenv';

dotenv.config();

const app: Express = express();
const port = process.env.PORT || 3000;
const BAD_REQUEST_STATUS_CODE = 400;

app.get('/hello-world', (req: Request, res: Response) => {
  res.send('Hello World!');
});

app.get('/http-client', (req: Request, res: Response) => {
  const link = req.query.link;

  if (typeof link !== 'string') {
    res
      .status(BAD_REQUEST_STATUS_CODE)
      .send("Query parameter 'link' must be a string");
    return;
  }

  if (!link) {
    res
      .status(BAD_REQUEST_STATUS_CODE)
      .send("Query parameter 'link' is required");
    return;
  }
  let url = null;

  try {
    url = new URL(link);
  } catch (err) {
    res.status(BAD_REQUEST_STATUS_CODE).send("Invalid 'link' URL");
    return;
  }

  fetch(url)
    .then((fetchResponse) => fetchResponse.text())
    .then((text) => {
      res.send(text);
    })
    .catch((err) => {
      res.status(BAD_REQUEST_STATUS_CODE).send(err);
    });
});

app.listen(port, () => {
  console.log(`[server]: Server is running at http://localhost:${port}`);
});
