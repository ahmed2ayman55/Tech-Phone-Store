import { Layout } from "@/components/layout";
import { useProduct } from "@/hooks/use-products";
import { useReviews, useCreateReview } from "@/hooks/use-reviews";
import { useCart } from "@/hooks/use-cart";
import { useAuth } from "@/hooks/use-auth";
import { useParams } from "wouter";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Textarea } from "@/components/ui/textarea";
import { useToast } from "@/hooks/use-toast";
import { ShoppingCart, Star, Truck, ShieldCheck, Check } from "lucide-react";
import { useState } from "react";
import { Skeleton } from "@/components/ui/skeleton";

export default function ProductDetail() {
  const { id } = useParams();
  const productId = Number(id);
  const { data: product, isLoading } = useProduct(productId);
  const { data: reviews } = useReviews(productId);
  const { user } = useAuth();
  const addItem = useCart((state) => state.addItem);
  const { toast } = useToast();
  
  const createReview = useCreateReview();
  const [rating, setRating] = useState(5);
  const [comment, setComment] = useState("");

  if (isLoading || !product) {
    return (
      <Layout>
        <div className="container mx-auto px-4 py-12">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
            <Skeleton className="h-[500px] w-full rounded-2xl" />
            <div className="space-y-6">
              <Skeleton className="h-12 w-3/4" />
              <Skeleton className="h-6 w-1/2" />
              <Skeleton className="h-24 w-full" />
            </div>
          </div>
        </div>
      </Layout>
    );
  }

  const handleAddToCart = () => {
    addItem(product);
    toast({
      title: "Added to cart",
      description: `${product.name} is now in your cart.`,
    });
  };

  const handleSubmitReview = (e: React.FormEvent) => {
    e.preventDefault();
    createReview.mutate(
      { productId, rating, comment },
      {
        onSuccess: () => {
          setComment("");
          toast({ title: "Review submitted!" });
        },
      }
    );
  };

  const specs = product.specs as Record<string, string>;

  return (
    <Layout>
      <div className="container mx-auto px-4 py-12">
        {/* Product Hero */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-20 mb-20">
          <div className="bg-white rounded-3xl p-8 flex items-center justify-center border shadow-sm">
            <img 
              src={product.imageUrl} 
              alt={product.name} 
              className="w-full max-h-[500px] object-contain hover:scale-105 transition-transform duration-500"
            />
          </div>
          
          <div className="space-y-8">
            <div>
              <div className="flex items-center gap-4 mb-4">
                <Badge variant="secondary" className="uppercase tracking-wide">{product.category}</Badge>
                {product.stock > 0 ? (
                  <Badge variant="outline" className="text-green-600 border-green-200 bg-green-50">In Stock</Badge>
                ) : (
                  <Badge variant="destructive">Out of Stock</Badge>
                )}
              </div>
              <h1 className="text-4xl md:text-5xl font-display font-bold mb-4">{product.name}</h1>
              <div className="flex items-center gap-2 mb-6">
                <div className="flex text-amber-500">
                  {[1, 2, 3, 4, 5].map((i) => (
                    <Star key={i} className={`w-5 h-5 ${i <= 4 ? "fill-current" : ""}`} />
                  ))}
                </div>
                <span className="text-muted-foreground">({reviews?.length || 0} reviews)</span>
              </div>
              <p className="text-lg text-muted-foreground leading-relaxed">
                {product.description}
              </p>
            </div>

            <div className="border-t border-b py-6">
              <div className="text-4xl font-bold mb-2">${Number(product.price).toFixed(2)}</div>
              <p className="text-sm text-muted-foreground">Free shipping on this item.</p>
            </div>

            <div className="flex flex-col gap-4">
              <Button 
                size="lg" 
                className="w-full h-14 text-lg rounded-xl shadow-lg shadow-primary/25"
                onClick={handleAddToCart}
                disabled={product.stock <= 0}
              >
                <ShoppingCart className="w-5 h-5 mr-2" />
                Add to Cart
              </Button>
              <div className="grid grid-cols-2 gap-4 text-sm text-muted-foreground">
                <div className="flex items-center gap-2">
                  <Truck className="w-4 h-4" /> 2-Day Shipping
                </div>
                <div className="flex items-center gap-2">
                  <ShieldCheck className="w-4 h-4" /> 2-Year Warranty
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Specs & Reviews */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
          {/* Specs */}
          <div className="lg:col-span-1 space-y-8">
            <h3 className="text-2xl font-bold font-display">Specifications</h3>
            <div className="bg-muted/30 rounded-2xl p-6 space-y-4">
              {Object.entries(specs).map(([key, value]) => (
                <div key={key} className="flex justify-between border-b border-border/50 pb-2 last:border-0 last:pb-0">
                  <span className="font-medium text-muted-foreground capitalize">{key}</span>
                  <span className="font-semibold text-right">{value}</span>
                </div>
              ))}
              {Object.keys(specs).length === 0 && (
                <p className="text-muted-foreground">No specifications listed.</p>
              )}
            </div>
          </div>

          {/* Reviews */}
          <div className="lg:col-span-2 space-y-8">
            <h3 className="text-2xl font-bold font-display">Customer Reviews</h3>
            
            {user ? (
              <form onSubmit={handleSubmitReview} className="bg-muted/30 rounded-2xl p-6 space-y-4">
                <h4 className="font-semibold">Write a review</h4>
                <div className="flex gap-2 mb-2">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <button
                      key={star}
                      type="button"
                      onClick={() => setRating(star)}
                      className="focus:outline-none"
                    >
                      <Star 
                        className={`w-6 h-6 ${star <= rating ? "fill-amber-500 text-amber-500" : "text-muted-foreground"}`} 
                      />
                    </button>
                  ))}
                </div>
                <Textarea
                  placeholder="Share your thoughts..."
                  value={comment}
                  onChange={(e) => setComment(e.target.value)}
                  className="bg-white"
                  required
                />
                <Button type="submit" disabled={createReview.isPending}>
                  {createReview.isPending ? "Submitting..." : "Submit Review"}
                </Button>
              </form>
            ) : (
              <div className="bg-muted/30 rounded-2xl p-6 text-center">
                <p className="mb-4">Please log in to leave a review.</p>
                <Button asChild variant="outline">
                  <a href="/api/login">Log In</a>
                </Button>
              </div>
            )}

            <div className="space-y-6">
              {reviews?.map((review) => (
                <div key={review.id} className="border-b pb-6 last:border-0">
                  <div className="flex items-center justify-between mb-2">
                    <div className="font-semibold">
                      {review.user?.firstName || "Anonymous"} {review.user?.lastName?.charAt(0)}
                    </div>
                    <div className="text-sm text-muted-foreground">
                      {new Date(review.createdAt!).toLocaleDateString()}
                    </div>
                  </div>
                  <div className="flex text-amber-500 mb-2">
                    {[...Array(review.rating)].map((_, i) => (
                      <Star key={i} className="w-4 h-4 fill-current" />
                    ))}
                  </div>
                  <p className="text-muted-foreground">{review.comment}</p>
                </div>
              ))}
              {reviews?.length === 0 && (
                <p className="text-muted-foreground italic">No reviews yet.</p>
              )}
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
}
