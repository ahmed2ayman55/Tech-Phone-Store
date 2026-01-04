import { Layout } from "@/components/layout";
import { useCart } from "@/hooks/use-cart";
import { useCreateOrder } from "@/hooks/use-orders";
import { useAuth } from "@/hooks/use-auth";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { useToast } from "@/hooks/use-toast";
import { useLocation } from "wouter";
import { Loader2 } from "lucide-react";
import { useEffect } from "react";

const checkoutSchema = z.object({
  line1: z.string().min(1, "Address is required"),
  city: z.string().min(1, "City is required"),
  zip: z.string().min(1, "ZIP code is required"),
  country: z.string().min(1, "Country is required"),
});

type CheckoutForm = z.infer<typeof checkoutSchema>;

export default function Checkout() {
  const { items, total, clearCart } = useCart();
  const { user, isLoading: authLoading } = useAuth();
  const createOrder = useCreateOrder();
  const { toast } = useToast();
  const [, setLocation] = useLocation();

  const form = useForm<CheckoutForm>({
    resolver: zodResolver(checkoutSchema),
  });

  useEffect(() => {
    if (!authLoading && !user) {
      window.location.href = "/api/login";
    }
  }, [user, authLoading]);

  if (items.length === 0) {
    setLocation("/cart");
    return null;
  }

  const onSubmit = (data: CheckoutForm) => {
    createOrder.mutate(
      {
        items: items.map((item) => ({
          productId: item.id,
          quantity: item.quantity,
        })),
        address: data,
      },
      {
        onSuccess: () => {
          clearCart();
          toast({
            title: "Order Placed!",
            description: "Your order has been successfully created.",
          });
          setLocation("/tracking");
        },
        onError: () => {
          toast({
            title: "Error",
            description: "Failed to place order. Please try again.",
            variant: "destructive",
          });
        },
      }
    );
  };

  if (authLoading || !user) {
    return (
      <Layout>
        <div className="h-screen flex items-center justify-center">
          <Loader2 className="animate-spin w-8 h-8 text-primary" />
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="container mx-auto px-4 py-12 max-w-4xl">
        <h1 className="text-3xl font-display font-bold mb-8">Checkout</h1>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          {/* Shipping Form */}
          <div>
            <h2 className="text-xl font-bold mb-6">Shipping Address</h2>
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <FormField
                  control={form.control}
                  name="line1"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Street Address</FormLabel>
                      <FormControl>
                        <Input placeholder="123 Tech Blvd" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />
                
                <div className="grid grid-cols-2 gap-4">
                  <FormField
                    control={form.control}
                    name="city"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>City</FormLabel>
                        <FormControl>
                          <Input placeholder="San Francisco" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  <FormField
                    control={form.control}
                    name="zip"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>ZIP Code</FormLabel>
                        <FormControl>
                          <Input placeholder="94103" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </div>
                
                <FormField
                  control={form.control}
                  name="country"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Country</FormLabel>
                      <FormControl>
                        <Input placeholder="United States" {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <div className="mt-8 pt-8 border-t">
                  <Button 
                    type="submit" 
                    size="lg" 
                    className="w-full"
                    disabled={createOrder.isPending}
                  >
                    {createOrder.isPending ? "Processing..." : `Pay $${total().toFixed(2)}`}
                  </Button>
                  <p className="text-xs text-muted-foreground text-center mt-4">
                    Secure checkout powered by Stripe.
                  </p>
                </div>
              </form>
            </Form>
          </div>

          {/* Order Summary */}
          <div className="bg-muted/30 rounded-2xl p-8 h-fit">
            <h2 className="text-xl font-bold mb-6">Order Summary</h2>
            <div className="space-y-4 mb-6">
              {items.map((item) => (
                <div key={item.id} className="flex justify-between items-center text-sm">
                  <div className="flex items-center gap-3">
                    <span className="font-bold text-muted-foreground">{item.quantity}x</span>
                    <span>{item.name}</span>
                  </div>
                  <span className="font-medium">${(Number(item.price) * item.quantity).toFixed(2)}</span>
                </div>
              ))}
            </div>
            <div className="border-t border-border/50 pt-4 flex justify-between font-bold text-lg">
              <span>Total</span>
              <span>${total().toFixed(2)}</span>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
}
