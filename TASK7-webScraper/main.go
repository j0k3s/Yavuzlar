package main

import (
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"regexp"
	"strings"
	"time"

	"github.com/PuerkitoBio/goquery"
)

func htmlCek(url string) (*http.Response, error) {
	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		return nil, fmt.Errorf("HTTP isteği hazırlanamadı: %v", err)
	}
	req.Header.Set("User-Agent", "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36")

	client := &http.Client{}
	yanit, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("HTTP isteği başarısız: %v", err)
	}
	return yanit, nil
}

func veriCek(url, baslikSecici string, ozelCek func(*goquery.Document) (string, []string)) (string, string, []string, error) {
	yanit, err := htmlCek(url)
	if err != nil {
		return "", "", nil, err
	}
	defer yanit.Body.Close()

	govde, err := io.ReadAll(yanit.Body)
	if err != nil {
		return "", "", nil, fmt.Errorf("Yanıt gövdesi okunamadı: %v", err)
	}
	html := string(govde)

	baslikRe := regexp.MustCompile(baslikSecici)
	baslikEslestirme := baslikRe.FindStringSubmatch(html)
	baslik := ""
	if len(baslikEslestirme) >= 2 {
		baslik = strings.TrimSpace(baslikEslestirme[1])
	}

	doc, err := goquery.NewDocumentFromReader(strings.NewReader(html))
	if err != nil {
		return "", "", nil, fmt.Errorf("HTML parse hatası: %v", err)
	}
	aciklama, tarihler := ozelCek(doc)

	return baslik, aciklama, tarihler, nil
}

func dosyayaKaydet(dosyaAdi, site, baslik, aciklama string, tarihler []string) error {
	dosya, err := os.OpenFile(dosyaAdi, os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		return fmt.Errorf("Dosya oluşturulamadı: %v", err)
	}
	defer dosya.Close()

	tarihIcerik := strings.Join(tarihler, "\n")
	icerik := fmt.Sprintf("Site: %s\nTarih:\n%s\nBaşlık: %s\nLink: %s\nÖzet: %s\n", site, tarihIcerik, baslik, aciklama, "Özet bulunamadı")
	_, err = dosya.WriteString(icerik + "\n==============================\n")
	if err != nil {
		return fmt.Errorf("Dosyaya yazılamadı: %v", err)
	}

	fmt.Printf("%s dosyasına başarıyla kaydedildi.\n", dosyaAdi)
	return nil
}

func menuGoster() {
	fmt.Println("\n--- Web Scraper Menü ---")
	fmt.Println("1. The Hacker News'ten veri çek")
	fmt.Println("2. VABS Haber Blog'dan veri çek")
	fmt.Println("3. Reddit'ten veri çek")
	fmt.Println("4. Çıkış")
	fmt.Print("Seçiminizi yapınız: ")
}

func main() {
	const dosyaAdi = "site_veriler.txt"
	os.Remove(dosyaAdi) 
	for {
		menuGoster()

		var secim int
		fmt.Scanln(&secim)

		switch secim {
		case 1:
			fmt.Println("The Hacker News'ten veri çekiliyor...")
			baslik, aciklama, tarihler, err := veriCek(
				"https://thehackernews.com/",
				`<title>(.*?)</title>`,
				func(doc *goquery.Document) (string, []string) {
					aciklama := doc.Find("meta[name='description']").AttrOr("content", "Açıklama bulunamadı")
					var tarihler []string
					doc.Find("time").Each(func(i int, s *goquery.Selection) {
						tarih := strings.TrimSpace(s.Text())
						if tarih != "" {
							tarihler = append(tarihler, tarih)
						}
					})
					return aciklama, tarihler
				},
			)
			if err != nil {
				log.Printf("Hata (The Hacker News): %v\n", err)
				continue
			}
			err = dosyayaKaydet(dosyaAdi, "The Hacker News", baslik, aciklama, tarihler)
			if err != nil {
				log.Printf("Dosya yazma hatası (The Hacker News): %v\n", err)
			}

		case 2:
			fmt.Println("VABS Haber Blog'dan veri çekiliyor...")
			baslik, aciklama, tarihler, err := veriCek(
				"https://www.vabs.com/haber-blog/",
				`<title>(.*?)</title>`,
				func(doc *goquery.Document) (string, []string) {
					aciklama := doc.Find("meta[property='og:description']").AttrOr("content", "Açıklama bulunamadı")
					var tarihler []string
					doc.Find("span").Each(func(i int, s *goquery.Selection) {
						tarih := strings.TrimSpace(s.Text())
						if regexp.MustCompile(`\d{2}\.\d{2}\.\d{4}`).MatchString(tarih) {
							tarihler = append(tarihler, tarih)
						}
					})
					return aciklama, tarihler
				},
			)
			if err != nil {
				log.Printf("Hata (VABS Haber Blog): %v\n", err)
				continue
			}
			err = dosyayaKaydet(dosyaAdi, "VABS", baslik, aciklama, tarihler)
			if err != nil {
				log.Printf("Dosya yazma hatası (VABS Haber Blog): %v\n", err)
			}

		case 3:
			fmt.Println("Reddit'ten veri çekiliyor...")
			resp, err := htmlCek("https://www.reddit.com/r/news/.json")
			if err != nil {
				log.Printf("Hata (Reddit): %v\n", err)
				continue
			}
			defer resp.Body.Close()

			var jsonResponse map[string]interface{}
			if err := json.NewDecoder(resp.Body).Decode(&jsonResponse); err != nil {
				log.Printf("JSON parse hatası: %v\n", err)
				continue
			}

			for _, child := range jsonResponse["data"].(map[string]interface{})["children"].([]interface{}) {
				dataItem := child.(map[string]interface{})["data"].(map[string]interface{})
				baslik := dataItem["title"].(string)
				link := dataItem["url"].(string)
				tarih := time.Unix(int64(dataItem["created"].(float64)), 0).Format("02-01-2006 15:04:05")

				err = dosyayaKaydet(dosyaAdi, "Reddit", baslik, link, []string{tarih})
				if err != nil {
					log.Printf("Dosya yazma hatası (Reddit): %v\n", err)
				}
			}

		case 4:
			fmt.Println("Programdan çıkılıyor...")
			return
		default:
			fmt.Println("Geçersiz seçim, lütfen tekrar deneyin.")
		}
	}
}