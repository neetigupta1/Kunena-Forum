package com.lesson1;

import static org.junit.Assert.assertTrue;

import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.Select;

import io.github.bonigarcia.wdm.WebDriverManager;

/**
 * Unit test for simple App.
 */
public class AppTest 
{
	static WebDriver driver;
   public static void main(String[] args) {
	WebDriverManager.chromedriver().setup();
	driver=new ChromeDriver();
	driver.get("http://dtnfarmerdev.prod.acquia-sites.com/user/login");
	driver.findElement(By.id("edit-name")).sendKeys("site_admin");
	driver.findElement(By.name("pass")).sendKeys("12345");
	driver.findElement(By.id("edit-submit")).click();
	driver.navigate().to("http://dtnfarmerdev.prod.acquia-sites.com/node/add/news");
    driver.findElement(By.xpath("//input[@id='edit-title-0-value']")).sendKeys("Test Web_Title");
    driver.findElement(By.id("edit-field-long-title-0-value")).sendKeys("Long_title");
    //driver.findElement(By.id("edit-field-copyright-1")).isEnabled();
    Select select = new Select(driver.findElement(By.id("edit-field-routing-shs-0-0")));
    select.deselectByVisibleText("Ag Newsroom");
    Select select1 = new Select(driver.findElement(By.id("edit-moderation-state-0-state")));
    select1.deselectByVisibleText("Published");
    driver.findElement(By.xpath("//input[@id='edit-submit']")).click();
    
    
    
}
	
}
