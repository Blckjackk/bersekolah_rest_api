"use client"

import * as React from "react"
import { cn } from "@/lib/utils"

interface SwitchProps extends React.InputHTMLAttributes<HTMLInputElement> {
  onCheckedChange?: (checked: boolean) => void;
}

const Switch = React.forwardRef<HTMLInputElement, SwitchProps>(
  ({ className, onCheckedChange, ...props }, ref) => {
    const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
      if (onCheckedChange) {
        onCheckedChange(event.target.checked);
      }
    };

    return (
      <label
        className={cn(
          "relative inline-flex items-center cursor-pointer",
          className
        )}
      >
        <input
          type="checkbox"
          className="sr-only peer"
          onChange={handleChange}
          ref={ref}
          {...props}
        />
        <div className="peer inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 bg-input peer-checked:bg-primary peer-focus:outline-none">
          <span className="pointer-events-none block h-4 w-4 rounded-full bg-background shadow-lg ring-0 transition-transform translate-x-0 peer-checked:translate-x-4"></span>
        </div>
      </label>
    );
  }
);

Switch.displayName = "Switch";

export { Switch };
