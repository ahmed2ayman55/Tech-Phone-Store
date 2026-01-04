import type { Express } from "express";
import { createServer, type Server } from "http";
import { setupAuth, registerAuthRoutes } from "./replit_integrations/auth";
import { storage } from "./storage";
import { api } from "@shared/routes";
import { z } from "zod";

export async function registerRoutes(httpServer: Server, app: Express): Promise<Server> {
  await setupAuth(app);
  registerAuthRoutes(app);

  // Products
  app.get(api.products.list.path, async (req, res) => {
    const search = req.query.search as string | undefined;
    const category = req.query.category as string | undefined;
    const products = await storage.getProducts(search, category);
    res.json(products);
  });

  app.get(api.products.get.path, async (req, res) => {
    const product = await storage.getProduct(Number(req.params.id));
    if (!product) return res.status(404).json({ message: "Product not found" });
    res.json(product);
  });

  app.post(api.products.create.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    // Add admin check here if needed
    try {
      const input = api.products.create.input.parse(req.body);
      const product = await storage.createProduct(input);
      res.status(201).json(product);
    } catch (error) {
       if (error instanceof z.ZodError) {
        res.status(400).json({ message: error.errors[0].message });
       } else {
         res.status(500).json({ message: "Internal Server Error" });
       }
    }
  });

  app.put(api.products.update.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    const product = await storage.updateProduct(Number(req.params.id), req.body);
    res.json(product);
  });

  app.delete(api.products.delete.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    await storage.deleteProduct(Number(req.params.id));
    res.status(204).send();
  });

  // Orders
  app.get(api.orders.list.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    const userId = (req.user as any).claims.sub;
    const orders = await storage.getOrders(userId);
    res.json(orders);
  });

  app.get(api.orders.get.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    const order = await storage.getOrder(Number(req.params.id));
    if (!order) return res.status(404).json({ message: "Order not found" });
    res.json(order);
  });

  app.post(api.orders.create.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    const userId = (req.user as any).claims.sub;
    
    try {
      const { items, address } = req.body;
      
      // Calculate total
      let total = 0;
      for (const item of items) {
         const p = await storage.getProduct(item.productId);
         if (p) total += Number(p.price) * item.quantity;
      }
  
      const orderData = {
        userId,
        total: total.toFixed(2),
        address,
        status: "pending"
      };
  
      const order = await storage.createOrder(orderData, items);
      res.status(201).json(order);
    } catch (error) {
      console.error(error);
      res.status(500).json({ message: "Failed to create order" });
    }
  });

  // Reviews
  app.get(api.reviews.list.path, async (req, res) => {
    const reviews = await storage.getReviews(Number(req.params.productId));
    res.json(reviews);
  });

  app.post(api.reviews.create.path, async (req, res) => {
    if (!req.isAuthenticated()) return res.status(401).json({ message: "Unauthorized" });
    const userId = (req.user as any).claims.sub;
    const reviewData = {
      userId,
      productId: Number(req.params.productId),
      ...req.body
    };
    const review = await storage.createReview(reviewData);
    res.status(201).json(review);
  });

  // Seed Data
  await seedDatabase();

  return httpServer;
}

async function seedDatabase() {
  const existing = await storage.getProducts();
  if (existing.length === 0) {
    await storage.createProduct({
      name: "iPhone 15 Pro",
      description: "The ultimate iPhone with titanium design.",
      price: "999.00",
      category: "Phones",
      imageUrl: "https://placehold.co/600x400/png?text=iPhone+15+Pro",
      stock: 50,
      specs: { color: "Natural Titanium", storage: "128GB" }
    });
    await storage.createProduct({
      name: "Samsung Galaxy S24 Ultra",
      description: "Galaxy AI is here.",
      price: "1299.00",
      category: "Phones",
      imageUrl: "https://placehold.co/600x400/png?text=Samsung+S24+Ultra",
      stock: 30,
      specs: { color: "Titanium Gray", storage: "256GB" }
    });
    await storage.createProduct({
      name: "AirPods Pro 2",
      description: "Adaptive Audio. Now playing.",
      price: "249.00",
      category: "Accessories",
      imageUrl: "https://placehold.co/600x400/png?text=AirPods+Pro+2",
      stock: 100,
      specs: { type: "In-Ear" }
    });
    console.log("Database seeded!");
  }
}
