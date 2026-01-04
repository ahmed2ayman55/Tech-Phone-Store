import { Link } from "wouter";
import { type Product } from "@shared/schema";
import { Card, CardContent, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { useCart } from "@/hooks/use-cart";
import { ShoppingCart, Star } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import { Badge } from "@/components/ui/badge";

export function ProductCard({ product }: { product: Product }) {
  const addItem = useCart((state) => state.addItem);
  const { toast } = useToast();

  const handleAddToCart = (e: React.MouseEvent) => {
    e.preventDefault();
    addItem(product);
    toast({
      title: "Added to cart",
      description: `${product.name} is now in your cart.`,
    });
  };

  return (
    <Link href={`/products/${product.id}`} className="block group h-full">
      <Card className="h-full border-transparent bg-card/50 hover:bg-card hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
        <div className="relative aspect-square bg-white p-6 flex items-center justify-center overflow-hidden">
          {product.stock <= 0 && (
            <div className="absolute top-3 left-3 z-10">
              <Badge variant="destructive">Out of Stock</Badge>
            </div>
          )}
          <img 
            src={product.imageUrl} 
            alt={product.name} 
            className="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
          />
        </div>
        <CardContent className="p-5 flex-1 flex flex-col">
          <div className="flex justify-between items-start mb-2">
            <p className="text-xs font-medium text-muted-foreground uppercase tracking-wider">{product.category}</p>
            <div className="flex items-center text-amber-500">
              <Star className="w-3 h-3 fill-current" />
              <span className="text-xs ml-1 font-medium">4.8</span>
            </div>
          </div>
          <h3 className="font-display font-bold text-lg mb-1 leading-tight group-hover:text-primary transition-colors line-clamp-2">
            {product.name}
          </h3>
          <p className="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1">
            {product.description}
          </p>
          <div className="font-bold text-xl text-foreground">
            ${Number(product.price).toFixed(2)}
          </div>
        </CardContent>
        <CardFooter className="p-5 pt-0">
          <Button 
            onClick={handleAddToCart} 
            className="w-full bg-secondary hover:bg-primary text-secondary-foreground hover:text-primary-foreground transition-all duration-300"
            disabled={product.stock <= 0}
          >
            <ShoppingCart className="w-4 h-4 mr-2" />
            Add to Cart
          </Button>
        </CardFooter>
      </Card>
    </Link>
  );
}
