import { Layout } from "@/components/layout";
import { useProducts } from "@/hooks/use-products";
import { ProductCard } from "@/components/product-card";
import { Button } from "@/components/ui/button";
import { Link } from "wouter";
import { ArrowRight, Zap, Shield, Truck } from "lucide-react";
import { Skeleton } from "@/components/ui/skeleton";

export default function Home() {
  const { data: products, isLoading } = useProducts();
  const featuredProducts = products?.slice(0, 4) || [];

  return (
    <Layout>
      {/* Hero Section */}
      <section className="relative overflow-hidden bg-gradient-to-br from-background to-secondary/5 pt-20 pb-32">
        <div className="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div className="space-y-8 animate-in slide-in-from-left duration-700">
            <h1 className="text-5xl md:text-7xl font-display font-extrabold leading-tight tracking-tight">
              Next Gen <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent">
                Tech Is Here
              </span>
            </h1>
            <p className="text-lg md:text-xl text-muted-foreground max-w-lg leading-relaxed">
              Discover premium devices engineered for professionals. 
              Uncompromising performance meets stunning design.
            </p>
            <div className="flex flex-col sm:flex-row gap-4">
              <Link href="/products">
                <Button size="lg" className="px-8 text-lg h-14 rounded-xl shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-1 transition-all">
                  Shop Now
                </Button>
              </Link>
              <Link href="/products?category=accessories">
                <Button variant="outline" size="lg" className="px-8 text-lg h-14 rounded-xl hover:bg-secondary/5 hover:-translate-y-1 transition-all">
                  View Accessories
                </Button>
              </Link>
            </div>
          </div>
          
          <div className="relative animate-in zoom-in duration-1000 delay-200">
            <div className="absolute inset-0 bg-gradient-to-tr from-primary/20 to-accent/20 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            {/* Unsplash tech image */}
            <img 
              src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?q=80&w=2929&auto=format&fit=crop" 
              alt="Premium Phone" 
              className="relative z-10 w-full max-w-lg mx-auto drop-shadow-2xl rounded-3xl transform rotate-[-6deg] hover:rotate-0 transition-transform duration-700 ease-out"
            />
          </div>
        </div>
      </section>

      {/* Features Grid */}
      <section className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="p-8 rounded-2xl bg-secondary/5 hover:bg-secondary/10 transition-colors">
              <Zap className="w-10 h-10 text-primary mb-4" />
              <h3 className="text-xl font-bold mb-2">Lightning Fast</h3>
              <p className="text-muted-foreground">Powered by the latest processors for unmatched speed.</p>
            </div>
            <div className="p-8 rounded-2xl bg-secondary/5 hover:bg-secondary/10 transition-colors">
              <Shield className="w-10 h-10 text-primary mb-4" />
              <h3 className="text-xl font-bold mb-2">Secure & Reliable</h3>
              <p className="text-muted-foreground">Enterprise-grade security features built into every device.</p>
            </div>
            <div className="p-8 rounded-2xl bg-secondary/5 hover:bg-secondary/10 transition-colors">
              <Truck className="w-10 h-10 text-primary mb-4" />
              <h3 className="text-xl font-bold mb-2">Free Shipping</h3>
              <p className="text-muted-foreground">Complimentary express shipping on all orders over $500.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section className="py-24 bg-muted/30">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold font-display mb-4">Featured Devices</h2>
              <p className="text-muted-foreground">Our most popular products this week.</p>
            </div>
            <Link href="/products">
              <Button variant="ghost" className="group">
                View All <ArrowRight className="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" />
              </Button>
            </Link>
          </div>

          {isLoading ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {[1, 2, 3, 4].map((i) => (
                <div key={i} className="space-y-4">
                  <Skeleton className="h-64 w-full rounded-xl" />
                  <Skeleton className="h-4 w-3/4" />
                  <Skeleton className="h-4 w-1/2" />
                </div>
              ))}
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {featuredProducts.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}
        </div>
      </section>
      
      {/* Banner */}
      <section className="py-24 container mx-auto px-4">
        <div className="rounded-3xl bg-black text-white p-12 md:p-24 relative overflow-hidden flex flex-col items-center text-center">
          <div className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1550745165-9bc0b252726f?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center opacity-40"></div>
          <div className="relative z-10 max-w-2xl mx-auto space-y-6">
            <h2 className="text-4xl md:text-6xl font-display font-black">Future Ready</h2>
            <p className="text-lg md:text-xl text-gray-300">
              Upgrade your workflow with our pro-grade workstations.
            </p>
            <Link href="/products">
              <Button size="lg" className="bg-white text-black hover:bg-white/90 rounded-full px-8">
                Explore Collection
              </Button>
            </Link>
          </div>
        </div>
      </section>
    </Layout>
  );
}
