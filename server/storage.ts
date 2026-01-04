import { products, orders, orderItems, reviews, type Product, type Order, type OrderItem, type Review, type InsertProduct, type InsertOrder, type InsertOrderItem, type InsertReview } from "@shared/schema";
import { db } from "./db";
import { eq, ilike, desc } from "drizzle-orm";

export interface IStorage {
  // Products
  getProducts(search?: string, category?: string): Promise<Product[]>;
  getProduct(id: number): Promise<Product | undefined>;
  createProduct(product: InsertProduct): Promise<Product>;
  updateProduct(id: number, product: Partial<InsertProduct>): Promise<Product>;
  deleteProduct(id: number): Promise<void>;

  // Orders
  getOrders(userId?: string): Promise<(Order & { items: (OrderItem & { product: Product })[] })[]>;
  getOrder(id: number): Promise<(Order & { items: (OrderItem & { product: Product })[] }) | undefined>;
  createOrder(order: InsertOrder, items: { productId: number; quantity: number }[]): Promise<Order>;

  // Reviews
  getReviews(productId: number): Promise<(Review & { user: { firstName: string | null; lastName: string | null } })[]>;
  createReview(review: InsertReview): Promise<Review>;
}

export class DatabaseStorage implements IStorage {
  async getProducts(search?: string, category?: string): Promise<Product[]> {
    let query = db.select().from(products);
    // Note: Drizzle select().from() returns a query builder, but we need to chain where/etc properly.
    // For simplicity with optional filters, we can build the query step by step or just use conditions.
    
    // Actually, simple way:
    const conditions = [];
    if (category) conditions.push(eq(products.category, category));
    if (search) conditions.push(ilike(products.name, `%${search}%`));
    
    // drizzle doesn't support array of conditions in .where() directly like that easily without `and()`.
    // Let's import `and`.
    const { and } = await import("drizzle-orm");
    
    if (conditions.length > 0) {
      return await db.select().from(products).where(and(...conditions));
    }
    
    return await db.select().from(products);
  }

  async getProduct(id: number): Promise<Product | undefined> {
    const [product] = await db.select().from(products).where(eq(products.id, id));
    return product;
  }

  async createProduct(product: InsertProduct): Promise<Product> {
    const [newProduct] = await db.insert(products).values(product).returning();
    return newProduct;
  }

  async updateProduct(id: number, product: Partial<InsertProduct>): Promise<Product> {
    const [updated] = await db.update(products).set(product).where(eq(products.id, id)).returning();
    return updated;
  }

  async deleteProduct(id: number): Promise<void> {
    await db.delete(products).where(eq(products.id, id));
  }

  async getOrders(userId?: string): Promise<(Order & { items: (OrderItem & { product: Product })[] })[]> {
    const ordersList = await db.query.orders.findMany({
      where: userId ? eq(orders.userId, userId) : undefined,
      with: {
        items: {
          with: {
            product: true
          }
        }
      },
      orderBy: [desc(orders.createdAt)]
    });
    return ordersList as any;
  }

  async getOrder(id: number): Promise<(Order & { items: (OrderItem & { product: Product })[] }) | undefined> {
    const order = await db.query.orders.findFirst({
      where: eq(orders.id, id),
      with: {
        items: {
          with: {
            product: true
          }
        }
      }
    });
    return order as any;
  }

  async createOrder(orderData: InsertOrder, items: { productId: number; quantity: number }[]): Promise<Order> {
    return await db.transaction(async (tx) => {
      const [order] = await tx.insert(orders).values(orderData).returning();
      
      for (const item of items) {
        const [product] = await tx.select().from(products).where(eq(products.id, item.productId));
        if (!product) throw new Error(`Product ${item.productId} not found`);

        await tx.insert(orderItems).values({
          orderId: order.id,
          productId: item.productId,
          quantity: item.quantity,
          price: product.price,
        });
      }
      return order;
    });
  }

  async getReviews(productId: number): Promise<(Review & { user: { firstName: string | null; lastName: string | null } })[]> {
    const reviewsList = await db.query.reviews.findMany({
      where: eq(reviews.productId, productId),
      with: {
        user: {
          columns: {
            firstName: true,
            lastName: true
          }
        }
      },
      orderBy: [desc(reviews.createdAt)]
    });
    return reviewsList as any;
  }

  async createReview(review: InsertReview): Promise<Review> {
    const [newReview] = await db.insert(reviews).values(review).returning();
    return newReview;
  }
}

export const storage = new DatabaseStorage();
