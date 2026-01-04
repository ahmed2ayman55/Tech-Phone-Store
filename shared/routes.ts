import { z } from 'zod';
import { insertProductSchema, insertOrderSchema, insertReviewSchema, products, orders, reviews, orderItems } from './schema';

export const errorSchemas = {
  validation: z.object({
    message: z.string(),
    field: z.string().optional(),
  }),
  notFound: z.object({
    message: z.string(),
  }),
  internal: z.object({
    message: z.string(),
  }),
};

export const api = {
  products: {
    list: {
      method: 'GET' as const,
      path: '/api/products',
      input: z.object({
        category: z.string().optional(),
        search: z.string().optional(),
      }).optional(),
      responses: {
        200: z.array(z.custom<typeof products.$inferSelect>()),
      },
    },
    get: {
      method: 'GET' as const,
      path: '/api/products/:id',
      responses: {
        200: z.custom<typeof products.$inferSelect>(),
        404: errorSchemas.notFound,
      },
    },
    create: {
      method: 'POST' as const,
      path: '/api/products',
      input: insertProductSchema,
      responses: {
        201: z.custom<typeof products.$inferSelect>(),
        400: errorSchemas.validation,
        401: errorSchemas.internal, // Unauthorized
      },
    },
    update: {
      method: 'PUT' as const,
      path: '/api/products/:id',
      input: insertProductSchema.partial(),
      responses: {
        200: z.custom<typeof products.$inferSelect>(),
        400: errorSchemas.validation,
        404: errorSchemas.notFound,
        401: errorSchemas.internal,
      },
    },
    delete: {
      method: 'DELETE' as const,
      path: '/api/products/:id',
      responses: {
        204: z.void(),
        404: errorSchemas.notFound,
        401: errorSchemas.internal,
      },
    },
  },
  orders: {
    list: {
      method: 'GET' as const,
      path: '/api/orders',
      responses: {
        200: z.array(z.custom<typeof orders.$inferSelect & { items: (typeof orderItems.$inferSelect & { product: typeof products.$inferSelect })[] }>()),
        401: errorSchemas.internal,
      },
    },
    get: {
      method: 'GET' as const,
      path: '/api/orders/:id',
      responses: {
        200: z.custom<typeof orders.$inferSelect & { items: (typeof orderItems.$inferSelect & { product: typeof products.$inferSelect })[] }>(),
        404: errorSchemas.notFound,
        401: errorSchemas.internal,
      },
    },
    create: {
      method: 'POST' as const,
      path: '/api/orders',
      input: z.object({
        items: z.array(z.object({
          productId: z.number(),
          quantity: z.number(),
        })),
        address: z.object({
          line1: z.string(),
          city: z.string(),
          zip: z.string(),
          country: z.string(),
        }),
      }),
      responses: {
        201: z.custom<typeof orders.$inferSelect>(),
        400: errorSchemas.validation,
        401: errorSchemas.internal,
      },
    },
  },
  reviews: {
    list: {
      method: 'GET' as const,
      path: '/api/products/:productId/reviews',
      responses: {
        200: z.array(z.custom<typeof reviews.$inferSelect & { user: { firstName: string | null, lastName: string | null } }>()),
      },
    },
    create: {
      method: 'POST' as const,
      path: '/api/products/:productId/reviews',
      input: z.object({
        rating: z.number().min(1).max(5),
        comment: z.string().optional(),
      }),
      responses: {
        201: z.custom<typeof reviews.$inferSelect>(),
        400: errorSchemas.validation,
        401: errorSchemas.internal,
      },
    },
  }
};

export function buildUrl(path: string, params?: Record<string, string | number>): string {
  let url = path;
  if (params) {
    Object.entries(params).forEach(([key, value]) => {
      if (url.includes(`:${key}`)) {
        url = url.replace(`:${key}`, String(value));
      }
    });
  }
  return url;
}
