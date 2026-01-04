import { Layout } from "@/components/layout";
import { useOrders } from "@/hooks/use-orders";
import { useAuth } from "@/hooks/use-auth";
import { Package, Clock, Truck, CheckCircle, AlertCircle } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

export default function Tracking() {
  const { user, isLoading: authLoading } = useAuth();
  const { data: orders, isLoading: ordersLoading } = useOrders();

  if (authLoading) return null;

  if (!user) {
    return (
      <Layout>
        <div className="container mx-auto px-4 py-24 text-center">
          <h2 className="text-2xl font-bold mb-4">Please log in to view your orders</h2>
          <Button asChild>
            <a href="/api/login">Log In</a>
          </Button>
        </div>
      </Layout>
    );
  }

  const getStatusIcon = (status: string) => {
    switch (status) {
      case "pending": return <Clock className="w-5 h-5 text-amber-500" />;
      case "processing": return <Package className="w-5 h-5 text-blue-500" />;
      case "shipped": return <Truck className="w-5 h-5 text-purple-500" />;
      case "delivered": return <CheckCircle className="w-5 h-5 text-green-500" />;
      case "cancelled": return <AlertCircle className="w-5 h-5 text-red-500" />;
      default: return <Clock className="w-5 h-5" />;
    }
  };

  return (
    <Layout>
      <div className="container mx-auto px-4 py-12 max-w-4xl">
        <h1 className="text-3xl font-display font-bold mb-8">Order History</h1>

        {ordersLoading ? (
          <div className="space-y-4">
            {[1, 2].map((i) => (
              <Skeleton key={i} className="h-40 w-full rounded-2xl" />
            ))}
          </div>
        ) : orders && orders.length > 0 ? (
          <div className="space-y-6">
            {orders.map((order) => (
              <Card key={order.id} className="overflow-hidden">
                <CardHeader className="bg-muted/20 pb-4 border-b">
                  <div className="flex justify-between items-center">
                    <div>
                      <p className="text-sm text-muted-foreground">Order #{order.id}</p>
                      <p className="text-xs text-muted-foreground">
                        Placed on {new Date(order.createdAt!).toLocaleDateString()}
                      </p>
                    </div>
                    <div className="flex items-center gap-2">
                      {getStatusIcon(order.status)}
                      <Badge variant="outline" className="capitalize">
                        {order.status}
                      </Badge>
                    </div>
                  </div>
                </CardHeader>
                <CardContent className="pt-6">
                  <div className="space-y-4">
                    {order.items.map((item) => (
                      <div key={item.id} className="flex justify-between items-center">
                        <div className="flex items-center gap-4">
                          <div className="h-12 w-12 bg-muted rounded-md flex items-center justify-center p-1">
                             <img src={item.product.imageUrl} alt={item.product.name} className="h-full w-full object-contain" />
                          </div>
                          <div>
                            <p className="font-medium">{item.product.name}</p>
                            <p className="text-sm text-muted-foreground">Qty: {item.quantity}</p>
                          </div>
                        </div>
                        <p className="font-medium">${Number(item.price).toFixed(2)}</p>
                      </div>
                    ))}
                  </div>
                  <div className="mt-6 pt-4 border-t flex justify-between items-center">
                    <div className="text-sm text-muted-foreground">
                      {order.address.city}, {order.address.country}
                    </div>
                    <div className="text-lg font-bold">
                      Total: ${Number(order.total).toFixed(2)}
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        ) : (
          <div className="text-center py-12 border-2 border-dashed rounded-2xl">
            <h3 className="text-lg font-bold mb-2">No orders yet</h3>
            <p className="text-muted-foreground mb-4">Start shopping to see your orders here.</p>
            <Link href="/products">
              <Button>Shop Now</Button>
            </Link>
          </div>
        )}
      </div>
    </Layout>
  );
}
