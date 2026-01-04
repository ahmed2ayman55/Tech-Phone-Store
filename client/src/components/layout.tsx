import { Link, useLocation } from "wouter";
import { useAuth } from "@/hooks/use-auth";
import { useCart } from "@/hooks/use-cart";
import { ShoppingBag, User, LogOut, Menu, X, Smartphone, Package, ShieldCheck } from "lucide-react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Badge } from "@/components/ui/badge";

export function Layout({ children }: { children: React.ReactNode }) {
  const { user, logout } = useAuth();
  const cartItems = useCart((state) => state.items);
  const cartCount = cartItems.reduce((acc, item) => acc + item.quantity, 0);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [location] = useLocation();

  const navLinks = [
    { href: "/products", label: "Shop" },
    { href: "/tracking", label: "Track Order" },
  ];

  return (
    <div className="min-h-screen flex flex-col bg-background font-sans">
      <header className="sticky top-0 z-50 w-full border-b bg-background/80 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div className="container mx-auto px-4 h-16 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2 font-display font-bold text-2xl text-primary hover:opacity-90 transition-opacity">
            <Smartphone className="w-8 h-8" />
            <span>TechStore</span>
          </Link>

          {/* Desktop Nav */}
          <nav className="hidden md:flex items-center gap-8">
            {navLinks.map((link) => (
              <Link 
                key={link.href} 
                href={link.href}
                className={`text-sm font-medium transition-colors hover:text-primary ${
                  location === link.href ? "text-primary" : "text-muted-foreground"
                }`}
              >
                {link.label}
              </Link>
            ))}
            {user?.email?.includes("admin") && (
              <Link 
                href="/admin" 
                className={`text-sm font-medium transition-colors hover:text-primary ${
                  location === "/admin" ? "text-primary" : "text-muted-foreground"
                }`}
              >
                Admin
              </Link>
            )}
          </nav>

          <div className="flex items-center gap-4">
            <Link href="/cart">
              <Button variant="ghost" size="icon" className="relative">
                <ShoppingBag className="h-5 w-5" />
                {cartCount > 0 && (
                  <Badge className="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center p-0 text-[10px]">
                    {cartCount}
                  </Badge>
                )}
              </Button>
            </Link>

            {user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" size="icon" className="rounded-full overflow-hidden border border-border">
                    {user.profileImageUrl ? (
                      <img src={user.profileImageUrl} alt={user.firstName || "User"} className="h-full w-full object-cover" />
                    ) : (
                      <User className="h-5 w-5" />
                    )}
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-56">
                  <DropdownMenuLabel>
                    <div className="flex flex-col space-y-1">
                      <p className="text-sm font-medium leading-none">{user.firstName} {user.lastName}</p>
                      <p className="text-xs leading-none text-muted-foreground">{user.email}</p>
                    </div>
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem asChild>
                    <Link href="/tracking" className="cursor-pointer">
                      <Package className="mr-2 h-4 w-4" />
                      <span>My Orders</span>
                    </Link>
                  </DropdownMenuItem>
                  {user.email?.includes("admin") && (
                    <DropdownMenuItem asChild>
                      <Link href="/admin" className="cursor-pointer">
                        <ShieldCheck className="mr-2 h-4 w-4" />
                        <span>Admin Dashboard</span>
                      </Link>
                    </DropdownMenuItem>
                  )}
                  <DropdownMenuSeparator />
                  <DropdownMenuItem onClick={() => logout()} className="text-destructive focus:text-destructive cursor-pointer">
                    <LogOut className="mr-2 h-4 w-4" />
                    <span>Log out</span>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <Button asChild variant="default" size="sm" className="hidden md:flex">
                <a href="/api/login">Log In</a>
              </Button>
            )}

            <Button
              variant="ghost"
              size="icon"
              className="md:hidden"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </Button>
          </div>
        </div>
      </header>

      {/* Mobile Menu */}
      {mobileMenuOpen && (
        <div className="md:hidden border-b bg-background p-4 space-y-4 animate-in slide-in-from-top-5">
          <nav className="flex flex-col space-y-2">
            {navLinks.map((link) => (
              <Link 
                key={link.href} 
                href={link.href}
                className="px-4 py-2 hover:bg-muted rounded-md transition-colors font-medium"
                onClick={() => setMobileMenuOpen(false)}
              >
                {link.label}
              </Link>
            ))}
            {user?.email?.includes("admin") && (
              <Link 
                href="/admin" 
                className="px-4 py-2 hover:bg-muted rounded-md transition-colors font-medium"
                onClick={() => setMobileMenuOpen(false)}
              >
                Admin
              </Link>
            )}
            {!user && (
              <a 
                href="/api/login"
                className="px-4 py-2 bg-primary text-primary-foreground rounded-md text-center font-medium mt-4 block"
              >
                Log In
              </a>
            )}
          </nav>
        </div>
      )}

      <main className="flex-1">
        {children}
      </main>

      <footer className="border-t bg-muted/30 py-12 mt-12">
        <div className="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="space-y-4">
            <div className="flex items-center gap-2 font-display font-bold text-xl text-primary">
              <Smartphone className="w-6 h-6" />
              <span>TechStore</span>
            </div>
            <p className="text-sm text-muted-foreground">
              Premium technology for the modern professional. Quality you can trust.
            </p>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Shop</h4>
            <ul className="space-y-2 text-sm text-muted-foreground">
              <li><Link href="/products?category=phones" className="hover:text-primary">Phones</Link></li>
              <li><Link href="/products?category=laptops" className="hover:text-primary">Laptops</Link></li>
              <li><Link href="/products?category=accessories" className="hover:text-primary">Accessories</Link></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Support</h4>
            <ul className="space-y-2 text-sm text-muted-foreground">
              <li><Link href="/tracking" className="hover:text-primary">Order Status</Link></li>
              <li><a href="#" className="hover:text-primary">Contact Us</a></li>
              <li><a href="#" className="hover:text-primary">Returns</a></li>
            </ul>
          </div>
          <div>
            <h4 className="font-semibold mb-4">Newsletter</h4>
            <div className="flex gap-2">
              <input 
                type="email" 
                placeholder="Enter email" 
                className="bg-background border rounded-md px-3 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-primary/20"
              />
              <Button size="sm">Subscribe</Button>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
