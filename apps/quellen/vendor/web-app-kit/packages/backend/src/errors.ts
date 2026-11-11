export class AppError extends Error {
  statusCode = 500;
}

export class NotFoundError extends AppError {
  statusCode = 404;

  constructor(resource: string, id: string) {
    super(`${resource} not found: ${id}`);
    this.name = "NotFoundError";
  }
}

export class BadRequestError extends AppError {
  statusCode = 400;

  constructor(message: string) {
    super(message);
    this.name = "BadRequestError";
  }
}

export class ValidationError extends AppError {
  statusCode = 422;

  constructor(message: string) {
    super(message);
    this.name = "ValidationError";
  }
}

export class UpstreamError extends AppError {
  statusCode = 502;

  constructor(message: string) {
    super(message);
    this.name = "UpstreamError";
  }
}

export class ConflictError extends AppError {
  statusCode = 409;

  constructor(message: string) {
    super(message);
    this.name = "ConflictError";
  }
}

export class UnauthorizedError extends AppError {
  statusCode = 401;

  constructor(message: string) {
    super(message);
    this.name = "UnauthorizedError";
  }
}

export class ConfigurationError extends Error {
  constructor(message: string) {
    super(message);
    this.name = "ConfigurationError";
  }
}
