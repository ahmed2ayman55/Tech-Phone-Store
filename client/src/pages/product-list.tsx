import { Layout } from "@/components/layout";
import { useProducts } from "@/hooks/use-products";
import { ProductCard } from "@/components/product-card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Search, SlidersHorizontal } from "lucide-react";
import { useState } from "react";
import { Link, useLocation } from "wouter";
import { Skeleton } from "@/components/ui/skeleton";

export default function ProductList() {
  const [search, setSearch] = useState("");
  const [location] = useLocation();
  const searchParams = new URLSearchParams(window.location.search);
  const category = searchParams.get("category") || undefined;
  
  const { data: products, isLoading } = useProducts({ search, category });

  const categories = [
    { id: "all", name: "All Products" },
    { id: "phones", name: "Smartphones" },
    { id: "laptops", name: "Laptops" },
    { id: "audio", name: "Audio" },
    { id: "accessories", name: "Accessories" },
  ];

  return (
    <Layout>
      <div className="bg-muted/30 py-12 border-b">
        <div className="container mx-auto px-4">
          <h1 className="text-4xl font-display font-bold mb-4">Shop</h1>
          <div className="flex flex-col md:flex-row gap-4 items-center">
            <div className="relative flex-1 w-full">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
              <Input 
                placeholder="Search products..." 
                className="pl-10 h-12 bg-background border-border"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
            <div className="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 scrollbar-hide">
              {categories.map((cat) => (
                <Link key={cat.id} href={cat.id === "all" ? "/products" : `/products?category=${cat.id}`}>
                  <Button 
                    variant={(!category && cat.id === "all") || category === cat.id ? "default" : "outline"}
                    className="whitespace-nowrap"
                  >
                    {cat.name}
                  </Button>
                </Link>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        {isLoading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {[1, 2, 3, 4, 5, 6, 7, 8].map((i) => (
              <div key={i} className="space-y-4">
                <Skeleton className="h-64 w-full rounded-xl" />
                <Skeleton className="h-4 w-3/4" />
                <Skeleton className="h-4 w-1/2" />
              </div>
            ))}
          </div>
        ) : products && products.length > 0 ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        ) : (
          <div className="text-center py-24">
            <h3 className="text-2xl font-bold mb-2">No products found</h3>
            <p className="text-muted-foreground">Try adjusting your search or category filter.</p>
          </div>
        )}
      </div>
    </Layout>
  );
}
